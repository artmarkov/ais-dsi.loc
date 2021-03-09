<?php

namespace common\models\activities\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\activities\Activities;

/**
 * ActivitiesSearch represents the model behind the search form about `common\models\activities\Activities`.
 */
class ActivitiesSearch extends Activities
{
    public $start_timestamp_operand;
    public $end_timestamp_operand;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'auditory_id'], 'integer'],
            [['title', 'description', 'all_day'], 'safe'],
            [['start_timestamp', 'end_timestamp'], 'safe'],
            [['start_timestamp_operand'], 'string'],
            [['end_timestamp_operand'], 'string'],
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
        $query = Activities::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'auditory_id' => $this->auditory_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'all_day', $this->all_day]);

        $query->andFilterWhere([($this->start_timestamp_operand) ? $this->start_timestamp_operand : '=', 'start_timestamp', ($this->start_timestamp) ? strtotime($this->start_timestamp) : null]);
        $query->andFilterWhere([($this->end_timestamp_operand) ? $this->end_timestamp_operand : '=', 'end_timestamp', ($this->end_timestamp) ? strtotime($this->end_timestamp) : null]);

        return $dataProvider;
    }
}
