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
            [['studyplan_id', 'programm_id', 'student_id', 'plan_year', 'course', 'status', 'education_cat_id', 'studyplan_invoices_id', 'plan_year', 'studyplan_invoices_status', 'invoices_id'], 'integer'],
            [['invoices_summ'], 'number'],
            [['studyplan_subject_ids', 'subject_list', 'subject_type_list', 'subject_type_sect_list', 'subject_vid_list', 'teachers_list'], 'string'],
            [['month_time_fact', 'studentFio', 'invoices_date', 'payment_time', 'payment_time_fact'], 'safe'],
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
            'studyplan_id' => $this->studyplan_id,
            'programm_id' => $this->programm_id,
            'student_id' => $this->student_id,
            'plan_year' => $this->plan_year,
            'course' => $this->course,
            'status' => $this->status,
            'education_cat_id' => $this->education_cat_id,
            'studyplan_invoices_id' => $this->studyplan_invoices_id,
            'plan_year' => $this->plan_year,
            'studyplan_invoices_status' => $this->studyplan_invoices_status,
            'invoices_id' => $this->invoices_id,
            'month_time_fact' => $this->month_time_fact,
            'invoices_date' => $this->invoices_date,
            'payment_time' => $this->payment_time,
            'payment_time_fact' => $this->payment_time_fact,
            'invoices_summ' => $this->invoices_summ
        ]);

        if($this->studentFio) {
            $query->andFilterWhere([
                'student_id' => $this->studentFio,
            ]);
        }
        if ($this->subject_list) {
            $query->andFilterWhere(['like', 'subject_list', $this->subject_list]);

        }

        return $dataProvider;
    }
}
