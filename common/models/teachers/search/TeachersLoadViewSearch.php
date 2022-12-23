<?php

namespace common\models\teachers\search;

use common\models\teachers\TeachersLoadView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * TeachersLoadTeachersViewSearch represents the model behind the search form about `common\models\teachers\TeachersLoadView`.
 */
class TeachersLoadViewSearch extends TeachersLoadView
{
    public $query;

    public function __construct($query = false)
    {
        $this->query = $query ?: TeachersLoadView::find();
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'subject_sect_id', 'plan_year', 'teachers_load_id', 'direction_id', 'teachers_id'], 'integer'],
            [['load_time', 'load_time_consult', 'week_time', 'year_time_consult'], 'number'],
            [['studyplan_subject_list'], 'string'],
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
            'subject_sect_studyplan_id' => $this->subject_sect_studyplan_id,
            'studyplan_subject_id' => $this->studyplan_subject_id,
            'subject_sect_id' => $this->subject_sect_id,
            'plan_year' => $this->plan_year,
            'week_time' => $this->week_time,
            'year_time_consult' => $this->year_time_consult,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'load_time' => $this->load_time,
            'load_time_consult' => $this->load_time_consult,
        ]);
        if($this->studyplan_subject_list) {
            $query->andWhere(new \yii\db\Expression("studyplan_subject_list::text LIKE '%" . $this->studyplan_subject_list . "%'"));
        }
        return $dataProvider;
    }
}
