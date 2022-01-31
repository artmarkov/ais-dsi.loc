<?php

namespace common\models\subjectsect\search;

use common\models\subjectsect\SubjectScheduleTeachersView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SubjectScheduleTeachersViewSearch represents the model behind the search form about `common\models\subjectsect\SubjectScheduleTeachersView`.
 */
class SubjectScheduleTeachersViewSearch extends SubjectScheduleTeachersView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teachers_load_id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'plan_year', 'subject_schedule_id', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id'], 'integer'],
            [['teachers_load_week_time'], 'number'],
            [['studyplan_subject_list', 'description'], 'string'],
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
        $query = SubjectScheduleTeachersView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'subject_sect_studyplan_id' => SORT_ASC,
                    'direction_id' => SORT_ASC,
                    'week_day' => SORT_ASC,
                    'time_in' => SORT_ASC,
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
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'week_num' => $this->week_num,
            'week_day' => $this->week_day,
//            'time_in' => $this->time_in,
//            'time_out' => $this->time_out,
            'auditory_id' => $this->auditory_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'studyplan_subject_list', $this->studyplan_subject_list]);

        return $dataProvider;
    }
}
