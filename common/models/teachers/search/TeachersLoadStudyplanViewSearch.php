<?php

namespace common\models\teachers\search;

use common\models\teachers\TeachersLoadStudyplanView;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TeachersLoadViewSearch represents the model behind the search form about `common\models\teachers\TeachersLoadStudyplanView`.
 */
class TeachersLoadStudyplanViewSearch extends TeachersLoadStudyplanView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_subject_id', 'subject_sect_studyplan_id', 'subject_sect_id', 'studyplan_id', 'student_id', 'plan_year', 'status','teachers_load_id','direction_id', 'teachers_id'], 'integer'],
            [['load_time', 'load_time_consult', 'week_time', 'year_time_consult'], 'number'],
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
        $query = TeachersLoadStudyplanView::find();

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
            'year_time_consult' => $this->year_time_consult,
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'subject_sect_id' => $this->subject_sect_id,
            'studyplan_id' => $this->studyplan_id,
            'student_id' => $this->student_id,
            'plan_year' => $this->plan_year,
            'status' => $this->status,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'load_time' => $this->load_time,
            'load_time_consult' => $this->load_time_consult,
        ]);

        return $dataProvider;
    }
}
