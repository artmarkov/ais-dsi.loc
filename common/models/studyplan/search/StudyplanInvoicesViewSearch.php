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
    public $date_in;
    public $subject_id;
    public $subject_type_id;
    public $limited_status_id;
    public $direction_id;
    public $teachers_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_id', 'student_fio', 'plan_year', 'course', 'status', 'education_cat_id', 'studyplan_invoices_id', 'studyplan_mat_capital_flag', 'plan_year', 'studyplan_invoices_status', 'invoices_id', 'mat_capital_flag'], 'integer'],
            [['month_time_fact', 'invoices_date', 'payment_time', 'payment_time_fact'], 'integer'],
            [['invoices_summ'], 'number'],
            [['subject_list', 'subject_type_list', 'teachers_list'], 'string'],
            [['programm_id', 'student_id', 'date_in', 'invoices_reporting_month', 'subject_id', 'limited_status_id', 'subject_type_id', 'subject_form_id', 'direction_id', 'teachers_id'], 'safe'],
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

        if ($this->date_in) {
            $t = explode(".", $this->date_in);
            $this->date_in = mktime(0, 0, 0, $t[0], 1, $t[1]);

            $query->andWhere(['OR', ['=', 'invoices_reporting_month', $this->date_in], ['IS', 'invoices_reporting_month', NULL]]);

        }
        $query->andFilterWhere([
            'studyplan_id' => $this->studyplan_id,
            'course' => $this->course,
            'status' => $this->status,
            'subject_form_id' => $this->subject_form_id,
            'education_cat_id' => $this->education_cat_id,
            'studyplan_invoices_id' => $this->studyplan_invoices_id,
            'plan_year' => $this->plan_year,
            'studyplan_invoices_status' => $this->studyplan_invoices_status,
            'invoices_id' => $this->invoices_id,
            'month_time_fact' => $this->month_time_fact,
            'payment_time' => $this->payment_time,
            'payment_time_fact' => $this->payment_time_fact,
            'invoices_summ' => $this->invoices_summ
        ]);
        if ($this->mat_capital_flag == 1) {
            $query->andWhere(['=', 'mat_capital_flag', 1]); // andWhere !!
        }
        if ($this->studyplan_mat_capital_flag == 1) {
            $query->andFilterWhere(['=', 'studyplan_mat_capital_flag', 1]);
        }
        if ($this->student_fio) {
            $query->andFilterWhere(['like', 'student_fio', $this->student_fio]);
        }
        if ($this->programm_id) {
            $query->andWhere(new \yii\db\Expression("programm_id = any(string_to_array(:programm_id, ',')::int[])"), [':programm_id' => implode(',', $this->programm_id)]);
        }
        if ($this->student_id) {
            $query->andWhere(new \yii\db\Expression("student_id = any(string_to_array(:student_id, ',')::int[])"), [':student_id' => implode(',', $this->student_id)]);
        }
        if ($this->subject_id) {
            $query->andWhere(new \yii\db\Expression(":subject_id = any(string_to_array(subject_list, ',')::int[])"), [':subject_id' => $this->subject_id]);
        }
        if ($this->subject_type_id) {
            $query->andWhere(new \yii\db\Expression(":subject_type_id = any(string_to_array(subject_type_list, ',')::int[])"), [':subject_type_id' => $this->subject_type_id]);
        }
        if ($this->limited_status_id) {
            $query->andWhere(new \yii\db\Expression(":limited_status_id = any(string_to_array(limited_status_list, ',')::int[])"), [':limited_status_id' => $this->limited_status_id]);
        }
//
        if ($this->teachers_id) {
            $query->andWhere(new \yii\db\Expression(":teachers_id = any(string_to_array(teachers_list, ',')::int[])"), [':teachers_id' => $this->teachers_id]);
        }

        return $dataProvider;
    }
}
