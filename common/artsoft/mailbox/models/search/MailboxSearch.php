<?php

namespace artsoft\mailbox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use artsoft\mailbox\models\Mailbox;

/**
 * MailboxSearch represents the model behind the search form about `artsoft\mailbox\models\Mailbox`.
 */
class MailboxSearch extends Mailbox
{
    
    public $dateSearch_1;
    public $dateSearch_2;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sender_id', 'created_at', 'updated_at', 'deleted_at', 'status_post', 'status_del', 'statusDelTrash'], 'integer'],
            [['title', 'content', 'dateSearch_1', 'dateSearch_2'], 'safe'],
            ['gridReceiverSearch', 'string'],
            [['dateSearch_1', 'dateSearch_2'], 'date', 'format' => 'php:d.m.Y'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Mailbox::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                    'dateSearch_1' => SORT_DESC,
               ],
            'attributes' => [
                'dateSearch_1' => [
                    'asc' => ['mailbox.created_at' => SORT_ASC],
                    'desc' => ['mailbox.created_at' => SORT_DESC],
                ],
                'id', 
                'title', 
                'content',
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        
//        жадная загрузка
        $query->with(['receivers']);
        
         if ($this->gridReceiverSearch) {
            $query->joinWith(['receivers']);
        }
                 
        $query->andFilterWhere([
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'mailbox.created_at' => $this->created_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'mailbox.deleted_at' => $this->deleted_at,
            'status_post' => $this->status_post,
            'mailbox.status_del' => $this->status_del,
            'mailbox_inbox.receiver_id' => $this->gridReceiverSearch,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content]);
        
        $query->andFilterWhere(['>=', 'mailbox.created_at', $this->dateSearch_1 ? strtotime($this->dateSearch_1 . ' 00:00:00') : null])
            ->andFilterWhere(['<=', 'mailbox.created_at', $this->dateSearch_2 ? strtotime($this->dateSearch_2 . ' 23:59:59') : null]);
         
         if ($this->statusDelTrash)
        {
        
            $query->joinWith(['receivers']);
            $query->andFilterWhere(['OR', 
                ['AND', ['=', 'mailbox.sender_id', Yii::$app->user->identity->id], ['=', 'mailbox.status_del', $this->statusDelTrash]],
                ['AND', ['=', 'mailbox_inbox.receiver_id', Yii::$app->user->identity->id],['=', 'mailbox_inbox.status_del', $this->statusDelTrash]]]);
            
            $query->select(['mailbox.id', 'title', 'content', 'mailbox.created_at', 'sender_id'])->distinct();
        }
        return $dataProvider;
    }
}
