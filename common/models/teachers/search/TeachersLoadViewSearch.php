<?php

namespace common\models\teachers\search;

use common\models\teachers\TeachersLoadView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TeachersLoadViewSearch represents the model behind the search form about `common\models\teachers\TeachersLoadView`.
 */
class TeachersLoadViewSearch extends TeachersLoadView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_subject_id', 'subject_sect_studyplan_id', 'studyplan_id', 'student_id', 'plan_year', 'status','teachers_load_id','direction_id', 'teachers_id'], 'integer'],
            [['load_time', 'week_time'], 'number'],
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
        $query = TeachersLoadView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => false,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'week_time' => $this->week_time,
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'studyplan_id' => $this->studyplan_id,
            'student_id' => $this->student_id,
            'plan_year' => $this->plan_year,
            'status' => $this->status,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'load_time' => $this->load_time,
        ]);

        return $dataProvider;
    }
}
