<?php

namespace common\models\info\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\info\Document;

/**
 * DocumentSearch represents the model behind the search form about `common\models\info\Document`.
 */
class DocumentSearch extends Document
{
    public $fullName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_common_id'], 'integer'],
            ['fullName', 'string'],
            [['title', 'description', 'doc_date'], 'safe'],
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
        $query = Document::find()->joinWith('userCommon');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'attributes' => [
                'id',
                'user_common_id',
                'title',
                'doc_date',
                'description',
                'fullName' => [
                    'asc' => ['last_name' => SORT_ASC, 'first_name' => SORT_ASC, 'middle_name' => SORT_ASC],
                    'desc' => ['last_name' => SORT_DESC, 'first_name' => SORT_DESC, 'middle_name' => SORT_DESC],
                ]
            ]
        ]);

        $this->load($params);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_common_id' => $this->user_common_id,
        ]);

        if ($this->doc_date) {
            $tmp = explode(' - ', $this->doc_date);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.doc_date',
                    strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }

        if ($this->fullName) {
            $query->andFilterWhere(['like', 'first_name', $this->fullName])
                ->orFilterWhere(['like', 'last_name', $this->fullName])
                ->orFilterWhere(['like', 'middle_name', $this->fullName]);

        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
