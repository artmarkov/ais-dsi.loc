<?php

namespace common\models\schedule\search;

use common\models\schedule\ConsultScheduleView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ConsultScheduleViewSearch represents the model behind the search form about `common\models\schedule\ConsultScheduleView`.
 */
class ConsultScheduleViewSearch extends ConsultScheduleView
{
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: ConsultScheduleView::find();
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_subject_id', 'subject_sect_studyplan_id', 'subject_sect_id', 'plan_year', 'teachers_load_id', 'direction_id', 'teachers_id', 'consult_schedule_id',  'auditory_id'], 'integer'],
            [['studyplan_subject_list', 'sect_name'], 'string'],
            [['description', 'datetime_in', 'datetime_out'], 'safe'],
            [['load_time_consult', 'year_time_consult'], 'number'],
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
                'defaultOrder' => [
                    'sect_name' => SORT_ASC,
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
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'studyplan_subject_list' => $this->studyplan_subject_list,
            'year_time_consult' => $this->year_time_consult,
            'subject_sect_id' => $this->subject_sect_id,
            'plan_year' => $this->plan_year,
            'load_time_consult' => $this->load_time_consult,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'consult_schedule_id' => $this->consult_schedule_id,
            'datetime_in' => $this->datetime_in,
            'datetime_out' => $this->datetime_out,
            'auditory_id' => $this->auditory_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'sect_name', $this->sect_name]);

        return $dataProvider;
    }
}
