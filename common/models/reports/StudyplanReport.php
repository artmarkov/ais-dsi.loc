<?php

namespace common\models\reports;

use artsoft\fileinput\models\FileManager;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\education\LessonProgressView;
use common\models\info\Document;
use common\models\teachers\Teachers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;


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

        //  echo '<pre>' . print_r($this->getData(), true) . '</pre>'; die();
        // $this->template_name = self::template_studyplan_history;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function getData()
    {
        $models = (new Query())->from('studyplan_hist')
            ->innerJoin('guide_subject_form', 'guide_subject_form.id=studyplan_hist.subject_form_id')
            ->select('guide_subject_form.name AS name, COUNT(DISTINCT studyplan_hist.id) AS qty')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['studyplan_hist.status' => 0])
            ->andWhere(['status_reason' => 5])
            ->andWhere(['op' => 'U'])
            ->andWhere(['between', 'updated_at', $this->timestamp_in, $this->timestamp_out])
            ->groupBy('guide_subject_form.name')
            ->all();
        $models2 = (new Query())->from('studyplan_hist')
            ->innerJoin('guide_subject_form', 'guide_subject_form.id=studyplan_hist.subject_form_id')
            ->select('guide_subject_form.name AS name, COUNT(DISTINCT studyplan_hist.id) AS qty')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['studyplan_hist.status' => 0])
            ->andWhere(['status_reason' => 5])
            ->andWhere(['op' => 'U'])
            ->groupBy('guide_subject_form.name')
            ->all();
        return [1 => $models, 2 => $models2];
    }

}
