<?php

namespace common\models\studyplan\search;

use common\models\studyplan\StudyplanInvoicesView;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StudyplanInvoicesViewSearch represents the model behind the search form about `common\models\studyplan\StudyplanInvoicesView`.
 */
class StudyplanInvoicesViewSearch extends StudyplanInvoicesView
{
    public $studentFio;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_subject_id', 'subject_type_id', 'subject_vid_id', 'education_cat_id', 'course', 'studyplan_id', 'programm_id', 'student_id', 'plan_year', 'status', 'teachers_load_id', 'direction_id', 'teachers_id', 'studyplan_invoices_id', 'invoices_id', 'studyplan_invoices_status'], 'integer'],
            [['invoices_summ', 'week_time', 'load_time'], 'number'],
            [['month_time_fact', 'studentFio', 'invoices_date'], 'safe'],
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
        $query = StudyplanInvoicesView::find();

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
            'subject_type_id' => $this->subject_type_id,
            'subject_vid_id' => $this->subject_vid_id,
            'education_cat_id' => $this->education_cat_id,
            'course' => $this->course,
            'week_time' => $this->week_time,
            'studyplan_id' => $this->studyplan_id,
            'programm_id' => $this->programm_id,
            'student_id' => $this->student_id,
            'plan_year' => $this->plan_year,
            'status' => $this->status,
            'teachers_load_id' => $this->teachers_load_id,
            'direction_id' => $this->direction_id,
            'teachers_id' => $this->teachers_id,
            'studyplan_invoices_id' => $this->studyplan_invoices_id,
            'invoices_id' => $this->invoices_id,
            'invoices_summ' => $this->invoices_summ,
            'invoices_date' => $this->invoices_date,
            'week_time' => $this->week_time,
            'load_time' => $this->load_time,
        ]);

        if($this->studentFio) {
            $query->andFilterWhere([
                'student_id' => $this->studentFio,
            ]);
        }

        return $dataProvider;
    }
}
