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
    public $date_out;
    public $subject_id;
    public $subject_type_id;
    public $subject_type_sect_id;
    public $subject_vid_id;
    public $direction_id;
    public $teachers_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studyplan_id', 'programm_id', 'student_id', 'plan_year', 'course', 'status', 'education_cat_id', 'studyplan_invoices_id', 'plan_year', 'studyplan_invoices_status', 'invoices_id'], 'integer'],
            [['month_time_fact', 'invoices_date', 'payment_time', 'payment_time_fact'], 'integer'],
            [['invoices_summ'], 'number'],
            [['studyplan_subject_ids', 'subject_list', 'subject_type_list', 'subject_type_sect_list', 'subject_vid_list', 'direction_list', 'teachers_list'], 'string'],
            [['date_in', 'date_out', 'subject_id', 'subject_type_id', 'subject_type_sect_id', 'subject_vid_id', 'direction_id', 'teachers_id'], 'safe'],
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
            'payment_time' => $this->payment_time,
            'payment_time_fact' => $this->payment_time_fact,
            'invoices_summ' => $this->invoices_summ
        ]);
        if ($this->date_in && $this->date_out) {
            $query->andWhere(['OR', ['between', 'invoices_date', Yii::$app->formatter->asTimestamp($this->date_in), Yii::$app->formatter->asTimestamp($this->date_out)], ['IS', 'invoices_date', NULL]]);
        }
        if ($this->subject_id) {
            $query->andWhere(new \yii\db\Expression(":subject_id = any(string_to_array(subject_list, ',')::int[])"), [':subject_id' => $this->subject_id]);
        }
        if ($this->subject_type_id) {
            $query->andWhere(new \yii\db\Expression(":subject_type_id = any(string_to_array(subject_type_list, ',')::int[])"), [':subject_type_id' => $this->subject_type_id]);
        }
        if ($this->subject_type_sect_id) {
            $query->andWhere(new \yii\db\Expression(":subject_type_sect_id = any(string_to_array(subject_type_sect_list, ',')::int[])"), [':subject_type_sect_id' => $this->subject_type_sect_id]);
        }
        if ($this->subject_vid_id) {
            $query->andWhere(new \yii\db\Expression(":subject_vid_id = any(string_to_array(subject_vid_list, ',')::int[])"), [':subject_vid_id' => $this->subject_vid_id]);
        }
        if ($this->direction_id) {
            $query->andWhere(new \yii\db\Expression(":direction_id = any(string_to_array(direction_list, ',')::int[])"), [':direction_id' => $this->direction_id]);
        }
        if ($this->teachers_id) {
            $query->andWhere(new \yii\db\Expression(":teachers_id = any(string_to_array(teachers_list, ',')::int[])"), [':teachers_id' => $this->teachers_id]);
        }

        return $dataProvider;
    }
}