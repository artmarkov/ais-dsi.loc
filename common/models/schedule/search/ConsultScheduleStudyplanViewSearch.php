<?php

namespace common\models\schedule\search;

use common\models\schedule\ConsultScheduleStudyplanView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ConsultScheduleStudyplanView represents the model behind the search form about `common\models\schedule\ConsultScheduleStudyplanView`.
 */
class ConsultScheduleStudyplanViewSearch extends ConsultScheduleStudyplanView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_subject_id', 'subject_sect_studyplan_id', 'subject_sect_id', 'studyplan_id', 'student_id', 'plan_year', 'status', 'teachers_load_id', 'direction_id', 'teachers_id', 'consult_schedule_id', 'auditory_id'], 'integer'],
            [['studyplan_subject_list'], 'string'],
            [['description', 'datetime_in', 'datetime_out'], 'safe'],
            [['load_time_consult', 'year_time_consult'], 'number'],
            [['sect_name', 'student_fio'], 'number'],
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
        $query = ConsultScheduleStudyplanView::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
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
            'year_time_consult' => $this->year_time_consult,
            'load_time_consult' => $this->load_time_consult,
            'studyplan_subject_list' => $this->studyplan_subject_list,
            'subject_sect_id' => $this->subject_sect_id,
            'studyplan_id' => $this->studyplan_id,
            'student_id' => $this->student_id,
            'plan_year' => $this->plan_year,
            'status' => $this->status,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'consult_schedule_id' => $this->consult_schedule_id,
            'datetime_in' => $this->datetime_in,
            'datetime_out' => $this->datetime_out,
            'auditory_id' => $this->auditory_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
