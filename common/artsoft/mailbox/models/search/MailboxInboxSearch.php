<?php

namespace artsoft\mailbox\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use artsoft\mailbox\models\MailboxInbox;

/**
 * MailboxInboxSearch represents the model behind the search form about `artsoft\mailbox\models\MailboxInbox`.
 */
class MailboxInboxSearch extends MailboxInbox
{
    public $dateSearch_1;
    public $dateSearch_2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'mailbox_id', 'receiver_id', 'deleted_at', 'status_del', 'status_read', 'mailboxStatusPost'], 'integer'],
            [['mailboxTitle', 'mailboxContent'], 'string'],
            [['dateSearch_1', 'dateSearch_2'], 'safe'],
            [['mailboxSenderId'], 'integer'],
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
        $query = MailboxInbox::find();

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

                'mailboxSenderId' => [
                    'asc' => ['mailbox.sender_id' => SORT_ASC],
                    'desc' => ['mailbox.sender_id' => SORT_DESC],
                ],

                'mailboxTitle' => [
                    'asc' => ['mailbox.title' => SORT_ASC],
                    'desc' => ['mailbox.title' => SORT_DESC],
                ],

                'mailboxContent' => [
                    'asc' => ['mailbox.content' => SORT_ASC],
                    'desc' => ['mailbox.content' => SORT_DESC],
                ],

                'dateSearch_1' => [
                    'asc' => ['mailbox.created_at' => SORT_ASC],
                    'desc' => ['mailbox.created_at' => SORT_DESC],
                ],
                'id',
                'status_read',
                'receiver_id',
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        жадная загрузка
        $query->joinWith(['mailbox']);


        $query->andFilterWhere([
            'id' => $this->id,
            'mailbox_id' => $this->mailbox_id,
            'receiver_id' => $this->receiver_id,
            'deleted_at' => $this->deleted_at,
            'mailbox.sender_id' => $this->mailboxSenderId,
            'mailbox.status_post' => $this->mailboxStatusPost,
            'mailbox_inbox.status_del' => $this->status_del,
            'status_read' => $this->status_read,
        ]);


        if ($this->mailboxTitle) {
            $query->joinWith(['mailbox' => function ($q) {
                $q->where('like', 'mailbox.title', $this->mailboxTitle);
            }]);
        }
        if ($this->mailboxContent) {
            $query->joinWith(['mailbox' => function ($q) {
                $q->where('like', 'mailbox.content', $this->mailboxContent);
            }]);
        }
        if ($this->dateSearch_1 && $this->dateSearch_2) {
            $query->joinWith(['mailbox' => function ($q) {
                $q->andFilterWhere(['>=', 'mailbox.created_at', $this->dateSearch_1 ? strtotime($this->dateSearch_1 . ' 00:00:00') : null])
                    ->andFilterWhere(['<=', 'mailbox.created_at', $this->dateSearch_2 ? strtotime($this->dateSearch_2 . ' 23:59:59') : null]);
            }]);
        }

        return $dataProvider;
    }
}
