<?php

namespace common\models\schedule\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\schedule\SubjectScheduleStudyplanView;

/**
 * SubjectScheduleViewSearch represents the model behind the search form about `common\models\schedule\SubjectScheduleStudyplanView`.
 */
class SubjectScheduleStudyplanViewSearch extends SubjectScheduleStudyplanView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_subject_id', 'subject_sect_studyplan_id', 'subject_sect_id', 'studyplan_id', 'student_id', 'plan_year','status','teachers_load_id', 'direction_id', 'teachers_id', 'subject_schedule_id', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id'], 'integer'],
            [['load_time', 'week_time'], 'number'],
            [['description', 'studyplan_subject_list'], 'string'],
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
        $query = SubjectScheduleStudyplanView::find();

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
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'studyplan_subject_list' => $this->studyplan_subject_list,
            'subject_sect_id' => $this->subject_sect_id,
            'studyplan_id' => $this->studyplan_id,
            'student_id' => $this->student_id,
            'plan_year' => $this->plan_year,
            'status' => $this->status,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'week_time' => $this->week_time,
            'load_time' => $this->load_time,
            'subject_schedule_id' => $this->subject_schedule_id,
            'week_num' => $this->week_num,
            'week_day' => $this->week_day,
            'time_in' => $this->time_in,
            'time_out' => $this->time_out,
            'auditory_id' => $this->auditory_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
