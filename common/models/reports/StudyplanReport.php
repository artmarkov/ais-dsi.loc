<?php

namespace common\models\reports;

use Yii;
use yii\db\Query;

class StudyplanReport
{
    protected $plan_year;
    protected $timestamp_in;
    protected $timestamp_out;

    public function __construct($model_date)
    {
        // $this->model_date = $model_date;
        $this->plan_year = $model_date->plan_year;
        // $timestamp = ArtHelper::getStudyYearParams($model_date->plan_year);
        $year = $this->plan_year + 1;
        $this->timestamp_in = mktime(0, 0, 0, 1, 1, $year);
        $this->timestamp_out = mktime(0, 0, 0, 6, 1, $year);

         // echo '<pre>' . print_r($this->getData(), true) . '</pre>'; die();
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function getData()
    {
        $models = (new Query())->from('studyplan_hist AS h')
            ->innerJoin('guide_subject_form', 'guide_subject_form.id=h.subject_form_id')
            ->select(new \yii\db\Expression("guide_subject_form.name AS name, COUNT(DISTINCT h.id) FILTER(WHERE h.updated_at BETWEEN '{$this->timestamp_in}' AND '{$this->timestamp_out}') AS qty, COUNT(DISTINCT h.id) AS qty_all"))
            ->where(['h.plan_year' => $this->plan_year])
            ->andWhere(['h.status' => 0])
            ->andWhere(['h.status_reason' => 5])
            ->andWhere(['h.op' => 'U'])
            ->groupBy('guide_subject_form.name')
            ->all();
        return $models;
    }

}
