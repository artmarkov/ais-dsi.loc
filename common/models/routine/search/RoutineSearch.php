<?php

namespace common\models\routine\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\routine\Routine;

/**
 * RoutineSearch represents the model behind the search form of `common\models\routine\Routine`.
 */
class RoutineSearch extends Routine
{
    public $start_timestamp_operand;
    public $end_timestamp_operand;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cat_id'], 'integer'],
            [['description', 'start_timestamp', 'end_timestamp'], 'safe'],
            [['start_timestamp_operand'], 'string'],
            [['end_timestamp_operand'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Routine::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cat_id' => $this->cat_id,
        ]);
        $query->andFilterWhere([($this->start_timestamp_operand) ? $this->start_timestamp_operand : '=', 'start_timestamp', ($this->start_timestamp) ? strtotime($this->start_timestamp) : null]);
        $query->andFilterWhere([($this->end_timestamp_operand) ? $this->end_timestamp_operand : '=', 'end_timestamp', ($this->end_timestamp) ? strtotime($this->end_timestamp) : null]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
