<?php

namespace common\models\execution;

use artsoft\helpers\ArtHelper;
use common\models\education\ProgressConfirm;
use common\models\education\ProgressConfirmIndiv;
use common\models\teachers\TeachersLoadView;
use common\models\user\UserCommon;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\Html;

/**
 * Class ExecutionProgressConfirm
 * @package common\models\execution
 *
 */
class ExecutionProgressConfirm
{
    protected $date_in;
    protected $plan_year;
    protected $timestamp_in;
    protected $timestamp_out;
    protected $teachers_id;
    protected $teachersIds;
    protected $teachersSubject;
    protected $teachersProgressConfirm;
    protected $teachersProgressIndivConfirm;

    public static function getData($model_date)
    {
        return new self($model_date);
    }

    public function __construct($model_date)
    {
        $this->teachers_id = $model_date->teachers_id ?? null;
        $this->date_in = $model_date->date_in;
        $this->getDateParams();
        $this->plan_year = ArtHelper::getStudyYearDefault(null, $this->timestamp_in);
        $this->teachersSubject = $this->getDataSubject();
        $this->teachersIds = $model_date->teachersIds ?? (!$this->teachers_id ? $this->getTeachersIds() : [$this->teachers_id]);
        $this->teachersProgressConfirm = $this->getProgressDataConfirm();
        $this->teachersProgressIndivConfirm = $this->getProgressDataIndivConfirm();
//        echo '<pre>' . print_r( $this->plan_year, true) . '</pre>';die();
    }

    protected function getDateParams()
    {
        $timestamp = ArtHelper::getMonYearParams($this->date_in);
        $this->timestamp_in = $timestamp[0];
        $this->timestamp_out = $timestamp[1];
    }

    protected function getDataSubject()
    {
        return TeachersLoadView::find()
            ->where(['!=', 'load_time', 0])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['status' => 1])
            ->asArray()
            ->all();
    }

    protected function getTeachersIds()
    {
        $teachersIds = ArrayHelper::getColumn($this->teachersSubject, 'teachers_id');
        return (new Query())->from('teachers_view')
            ->select('teachers_id')
            ->where(['teachers_id' => $teachersIds])
            ->andWhere(['status' => UserCommon::STATUS_ACTIVE])
            ->column();
    }

    protected function getProgressDataConfirm()
    {
        $models = ProgressConfirm::find()
            ->select(new \yii\db\Expression('subject_sect_studyplan_id,teachers_id,confirm_status'))
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['timestamp_month' => $this->timestamp_in])
            ->asArray()
            ->all();
        $data = ArrayHelper::index($models, 'subject_sect_studyplan_id', 'teachers_id');
        return $data;
    }

    protected function getProgressDataIndivConfirm()
    {
        $models = ProgressConfirmIndiv::find()
            ->select(new \yii\db\Expression('subject_key,teachers_id,confirm_status'))
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['timestamp_month' => $this->timestamp_in])
            ->asArray()
            ->all();
        $data = ArrayHelper::index($models, 'subject_key', 'teachers_id');
        return $data;
    }

    public function getDataTeachers()
    {
        $load_data = [];

        $attributes = ['teachers_id' => 'Преподаватели'];
        $attributes += ['scale_0' => 'Групповые/Мелкогрупповые'];
        $attributes += ['scale_1' => 'Индивидуальные'];

        $dataTeachers = ArrayHelper::index($this->teachersSubject, 'subject_sect_studyplan_id', 'teachers_id');
        $dataTeachersInd = ArrayHelper::index($this->teachersSubject, 'subject_key', 'teachers_id');
        foreach ($this->teachersIds as $i => $teachers_id) {
            $load_data[$teachers_id]['teachers_id'] = $teachers_id;
            $load_data[$teachers_id]['scale_0'] = '';
            $load_data[$teachers_id]['scale_1'] = '';
            foreach ($dataTeachers[$teachers_id] ?? [] as $subject_sect_studyplan_id => $value) {
                if ($subject_sect_studyplan_id == 0) continue;
                $check = $this->getCheckLabel($this->teachersProgressConfirm[$teachers_id] ?? [], $value, 'subject_sect_studyplan_id');
                $check = Html::a($check, ['/teachers/default/studyplan-progress', 'id' => $teachers_id, 'subject_sect_studyplan_id' => $value['subject_sect_studyplan_id'] . '||' . $this->timestamp_in], ['target' => '_blank', 'title' => $value['subject'] . ' ' . $value['sect_name']]);
                $load_data[$teachers_id]['scale_0'] .= $check;
            }
            foreach ($dataTeachersInd[$teachers_id] ?? [] as $subject_key => $value) {
                if ($subject_key == null) continue;
                $check = $this->getCheckLabel($this->teachersProgressIndivConfirm[$teachers_id] ?? [], $value, 'subject_key');
                $check = Html::a($check, ['/teachers/default/studyplan-progress-indiv', 'id' => $teachers_id, 'subject_key' => $value['subject_key'] . '||' . $this->timestamp_in], ['target' => '_blank', 'title' => $value['subject']]);
                $load_data[$teachers_id]['scale_1'] .= $check;
            }
        }
//        echo '<pre>' . print_r($load_data, true) . '</pre>';
        return ['data' => $load_data, 'attributes' => $attributes];
    }


    protected function getCheckLabel($data, $value, $key)
    {
        if (isset($data[$value[$key]])) {
            if ($data[$value[$key]]['confirm_status'] == 1) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
            } elseif ($data[$value[$key]]['confirm_status'] == 2) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: darkorange"></i>';
            } else {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: grey"></i>';
            }
        } else {
            $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>';
        }
        return $check;
    }


}