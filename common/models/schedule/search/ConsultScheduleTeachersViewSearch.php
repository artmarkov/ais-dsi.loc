<?php

namespace common\models\schedule\search;

use common\models\schedule\ConsultScheduleTeachersView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ConsultScheduleTeachersView represents the model behind the search form about `common\models\schedule\ConsultScheduleTeachersView`.
 */
class ConsultScheduleTeachersViewSearch extends ConsultScheduleTeachersView
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teachers_load_id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'plan_year', 'consult_schedule_id', 'auditory_id'], 'integer'],
            [['studyplan_subject_list'], 'string'],
            [['description', 'datetime_in', 'datetime_out'], 'safe'],
            [['year_time_consult'], 'number'],
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
        $query = ConsultScheduleTeachersView::find();

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
            'teachers_load_id' => $this->teachers_load_id,
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'course' => $this->course,
            'subject_cat_id' => $this->subject_cat_id,
            'subject_id' => $this->subject_id,
            'subject_type_id' => $this->subject_type_id,
            'subject_vid_id' => $this->subject_vid_id,
            'plan_year' => $this->plan_year,
            'consult_schedule_id' => $this->consult_schedule_id,
            'datetime_in' => $this->datetime_in,
            'datetime_out' => $this->datetime_out,
            'auditory_id' => $this->auditory_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
