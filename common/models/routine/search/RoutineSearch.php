<?php

namespace common\models\routine\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\routine\Routine;

/**
 * RoutineSearch represents the model behind the search form of `common\models\routine\Routine`.
 */
class RoutineSearch extends Routine
{
    public $start_date_operand;
    public $end_date_operand;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cat_id'], 'integer'],
            [['description', 'start_date', 'end_date'], 'safe'],
            [['start_date_operand'], 'string'],
            [['end_date_operand'], 'string'],
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
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'start_date' => SORT_ASC,
                ],
            ],
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
        $query->andFilterWhere([($this->start_date_operand) ? $this->start_date_operand : '=', 'start_date', ($this->start_date) ? strtotime($this->start_date) : null]);
        $query->andFilterWhere([($this->end_date_operand) ? $this->end_date_operand : '=', 'end_date', ($this->end_date) ? strtotime($this->end_date) : null]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
