<?php

namespace common\models\teachers\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\teachers\TeachersPlan;

/**
 * TeachersPlanSearch represents the model behind the search form about `common\models\teachers\TeachersPlan`.
 */
class TeachersPlanSearch extends TeachersPlan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'direction_id', 'teachers_id', 'plan_year', 'week_num', 'week_day', 'time_plan_in', 'time_plan_out', 'auditory_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['description'], 'safe'],
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
        $query = TeachersPlan::find();

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
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'plan_year' => $this->plan_year,
            'week_num' => $this->week_num,
            'week_day' => $this->week_day,
            'time_plan_in' => $this->time_plan_in,
            'time_plan_out' => $this->time_plan_out,
            'auditory_id' => $this->auditory_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
