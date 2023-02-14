<?php

namespace common\models\schedule\search;

use common\models\schedule\SubjectScheduleView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SubjectScheduleViewSearch represents the model behind the search form about `common\models\schedule\SubjectScheduleView`.
 */
class SubjectScheduleViewSearch extends SubjectScheduleView
{
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: SubjectScheduleView::find();
        parent::__construct();
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_subject_id', 'subject_sect_studyplan_id', 'subject_sect_id', 'plan_year', 'time_out', 'auditory_id',  'teachers_load_id', 'direction_id', 'teachers_id', 'load_time', 'subject_schedule_id', 'week_num', 'week_day', 'time_in'], 'integer'],
            [['load_time', 'week_time'], 'number'],
            [['studyplan_subject_list', 'description', 'sect_name', 'subject'], 'string'],
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
        $query = $this->query;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
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
            'subject_sect_id' => $this->subject_sect_id,
            'plan_year' => $this->plan_year,
            'week_time' => $this->week_time,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'load_time' => $this->load_time,
            'subject_schedule_id' => $this->subject_schedule_id,
            'week_num' => $this->week_num,
            'week_day' => $this->week_day,
            'time_in' => $this->time_in,
            'time_out' => $this->time_out,
            'auditory_id' => $this->auditory_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'studyplan_subject_list', $this->studyplan_subject_list]);

        return $dataProvider;
    }
}
