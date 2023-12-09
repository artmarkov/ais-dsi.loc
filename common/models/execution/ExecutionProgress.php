<?php

namespace common\models\execution;

use artsoft\Art;
use artsoft\helpers\ArtHelper;
use artsoft\widgets\Notice;
use common\models\education\LessonItemsProgressView;
use common\models\routine\Routine;
use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use common\models\studyplan\Studyplan;
use common\models\studyplan\ThematicView;
use common\models\teachers\TeachersLoadView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Yii;
use artsoft\widgets\Tooltip;
use yii\helpers\Html;

/**
 * Class ExecutionProgress
 * @package common\models\execution
 *
 */
class ExecutionProgress
{
    protected $date_in;
    protected $timestamp_in;
    protected $timestamp_out;
    protected $routine;
    protected $teachers_id;
    protected $teachersIds;
    protected $teachersActivities;
    protected $teachersProgress;

    public static function getData($model_date)
    {
        return new self($model_date);
    }

    public function __construct($model_date)
    {
        $this->teachers_id = $model_date->teachers_id ?? null;
        $this->date_in = $model_date->date_in;
        $this->getDateParams();
        $this->routine = $this->getRoutine();
        $this->teachersIds = $model_date->teachersIds ?? (!$this->teachers_id ? array_keys(\artsoft\helpers\RefBook::find('teachers_fio', 1)->getList()) : [$this->teachers_id]);
        $this->teachersActivities = $this->getTeachersActivities();
        $this->teachersProgress = $this->getProgressData();
//        echo '<pre>' . print_r($this->routine, true) . '</pre>';

    }

    protected function getDateParams()
    {
        $timestamp = ArtHelper::getMonYearParams($this->date_in);
        $this->timestamp_in = $timestamp[0];
        $this->timestamp_out = $timestamp[1];
    }

    /**
     * Запрос на календарь занятий преподавателя
     * @param $teachersIds
     * @return array
     */
    protected function getTeachersActivities()
    {
        $models = (new Query())->from('activities_schedule_view')
            ->select(new \yii\db\Expression('teachers_id, subject_sect_studyplan_id,studyplan_subject_id,title,datetime_in,datetime_out, date_trunc(\'day\',to_timestamp(datetime_in+10800))::date AS date, subject_key'))
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['status' => 1])
            ->orderBy('datetime_in')
            ->all();
        $data = ArrayHelper::index($models, null, ['teachers_id', 'date', 'subject_sect_studyplan_id', 'studyplan_subject_id']);
//        echo '<pre>' . print_r($data, true) . '</pre>';        die();
        return $data;
    }

    protected function getProgressData()
    {
        $models = (new Query())->from('lesson_items_progress_studyplan_view')
            ->select(new \yii\db\Expression('teachers_id, subject_sect_studyplan_id,studyplan_subject_id,lesson_date, date_trunc(\'day\',to_timestamp(lesson_date+10800))::date AS date'))
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['between', 'lesson_date', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['status' => 1])
            ->orderBy('lesson_date')
            ->all();
        $data = ArrayHelper::index($models, null, ['teachers_id', 'date', 'subject_sect_studyplan_id', 'studyplan_subject_id']);
//        echo '<pre>' . print_r($data, true) . '</pre>';
//        die();
        return $data;
    }

    public function getDataTeachers()
    {
        $load_data = [];

        $attributes = ['teachers_id' => 'Преподаватели'];
        $attributes += ['scale_0' => 'Групповые/Мелкогрупповые'];
        $attributes += ['scale_1' => 'Индивидуальные'];

        foreach ($this->teachersIds as $i => $teachers_id) {
            $dataTeachers = $this->teachersActivities[$teachers_id] ?? [];
            $load_data[$teachers_id]['teachers_id'] = $teachers_id;
            $load_data[$teachers_id]['scale_0'] = '';
            $load_data[$teachers_id]['scale_1'] = '';
            foreach ($dataTeachers as $date => $dataSect) {
                if($this->routine[$date]['isDayOff'] || $this->routine[$date]['isHolidays']) continue;
                foreach ($dataSect as $subject_sect_studyplan_id => $dataSubject) {
                    foreach ($dataSubject as $studyplan_subject_id => $values) {
                        foreach ($values as $i => $value) {
//                            echo '<pre>' . print_r($value, true) . '</pre>';
//                            echo '<pre>' . print_r($this->teachersProgress[$teachers_id], true) . '</pre>';
                            if ($subject_sect_studyplan_id == 0) {
                                $check = !isset($this->teachersProgress[$teachers_id][$date][$subject_sect_studyplan_id][$studyplan_subject_id]) ? '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>' : '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
                                $check = Html::a($check, [Art::isBackend() ? '/teachers/default/studyplan-progress-indiv' : '/execution/teachers/studyplan-progress-indiv', 'id' => $teachers_id, 'subject_key' => base64_encode($value['subject_key'] . '||' . $value['datetime_in'])], ['target' => '_blank', 'title' => $value['title'] . ': ' . Yii::$app->formatter->asDatetime($value['datetime_in']) . ' - ' . Yii::$app->formatter->asDatetime($value['datetime_out'])]);
                                $load_data[$teachers_id]['scale_1'] .= $check;
                            } else {
                                $check = !isset($this->teachersProgress[$teachers_id][$date][$subject_sect_studyplan_id]) ? '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>' : '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';

                                $check = Html::a($check, [Art::isBackend() ? '/teachers/default/studyplan-progress' : '/execution/teachers/studyplan-progress', 'id' => $teachers_id, 'subject_sect_studyplan_id' => $subject_sect_studyplan_id], ['target' => '_blank', 'title' => $value['title'] . ': ' . Yii::$app->formatter->asDatetime($value['datetime_in']) . ' - ' . Yii::$app->formatter->asDatetime($value['datetime_out'])]);;
                                $load_data[$teachers_id]['scale_0'] .= $check;
                            }
                        }
                    }
                }
            }
        }
//        echo '<pre>' . print_r($load_data, true) . '</pre>';
        return ['data' => $load_data, 'attributes' => $attributes];
    }

    protected function getRoutine()
    {
        $routine = [];
        $mon = date('n', $this->timestamp_in);
        $year = date('Y', $this->timestamp_in);
        $day_in = date('j', $this->timestamp_in);
        $day_out = date('j', $this->timestamp_out);

        for ($day = $day_in; $day <= $day_out; $day++) {
            $timestamp = mktime(12, 0, 0, $mon, $day, $year); // середина суток
            $date = Yii::$app->formatter->asDate($timestamp,'php:Y-m-d');
            $isDayOff = Routine::isDayOff($timestamp);
            $isHolidays = Routine::isHolidays($timestamp);
            $routine[$date] = [
                'isDayOff' => $isDayOff,
                'isHolidays' => $isHolidays
            ];
        }
        return $routine;
    }
//
//    protected function getCheckLabel($value)
//    {
//        if (isset($value['studyplan_thematic_id'])) {
//
//            if ($value['doc_status'] == 1) {
//                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
//            } elseif ($value['doc_status'] == 2) {
//                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: darkorange"></i>';
//            } else {
//                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: grey"></i>';
//            }
//        } else {
//            $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>';
//        }
//        return $check;
//
//    }
//

}