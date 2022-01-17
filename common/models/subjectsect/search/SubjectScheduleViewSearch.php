<?php

namespace common\models\subjectsect\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\subjectsect\SubjectScheduleView;

/**
 * SubjectScheduleViewSearch represents the model behind the search form about `common\models\subjectsect\SubjectScheduleView`.
 */
class SubjectScheduleViewSearch extends SubjectScheduleView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teachers_load_id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'studyplan_id', 'student_id', 'programm_id','speciality_id','course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'plan_year', 'subject_sect_schedule_id', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id', 'status'], 'integer'],
            [['teachers_load_week_time', 'week_time', 'year_time'], 'number'],
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
        $query = SubjectScheduleView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'studyplan_id' => SORT_DESC,
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
            'teachers_load_id' => $this->teachers_load_id,
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'teachers_load_week_time' => $this->teachers_load_week_time,
            'studyplan_id' => $this->studyplan_id,
            'student_id' => $this->student_id,
            'programm_id' => $this->programm_id,
            'speciality_id' => $this->speciality_id,
            'course' => $this->course,
            'subject_cat_id' => $this->subject_cat_id,
            'subject_id' => $this->subject_id,
            'subject_type_id' => $this->subject_type_id,
            'subject_vid_id' => $this->subject_vid_id,
            'week_time' => $this->week_time,
            'year_time' => $this->year_time,
            'plan_year' => $this->plan_year,
            'subject_sect_schedule_id' => $this->subject_sect_schedule_id,
            'week_num' => $this->week_num,
            'week_day' => $this->week_day,
            'time_in' => $this->time_in,
            'time_out' => $this->time_out,
            'auditory_id' => $this->auditory_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
