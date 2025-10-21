<?php

namespace console\jobs;

use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\models\schedule\SubjectScheduleConfirm;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class LessonAddTask.
 */
class LessonAddTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $day = date('d');
        $mon = date('m');
        $year = date('Y');

        $timestamp_in = \Yii::$app->formatter->asTimestamp(mktime(0, 0, 0, $mon, $day, $year));
        $timestamp_out = $timestamp_in + 86399;
        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault();

        // находим только согласованные рассписания
        $teachersIds = SubjectScheduleConfirm::find()
            ->select('teachers_id')
            ->where(['=', 'confirm_status', SubjectScheduleConfirm::STATUS_ACTIVE])
            ->andWhere(['=', 'plan_year', $plan_year])
            ->column();

        // находим все занятия согласно согласованным расписаниям
        $active = (new Query())->from('activities_schedule_studyplan_view')
            ->select('studyplan_subject_id, subject_sect_studyplan_id')
            ->distinct()
            ->where(['between', 'datetime_in', $timestamp_in, $timestamp_out])
            ->andWhere(['teachers_id' => $teachersIds])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['status' => 1])
            ->orderBy('subject_sect_studyplan_id')
            ->all();
        $active = ArrayHelper::index($active, null, ['subject_sect_studyplan_id']);

        $lesson_items = (new Query())->from('lesson_items')
            ->where(['=', 'lesson_date', $timestamp_in])
            ->all();
        $lesson_items = ArrayHelper::index($lesson_items, null, ['subject_sect_studyplan_id', 'studyplan_subject_id']);

        foreach ($active as $subject_sect_studyplan_id => $data) {
            if ($subject_sect_studyplan_id != 0) {
                if ($model = $this->setLesson($lesson_items, 0, $subject_sect_studyplan_id, $timestamp_in)) {
                    foreach ($data as $item => $dataItem) {
                        $this->setProgress($model, $dataItem);
                    }
                }
            } else {
                foreach ($data as $item => $dataItem) {
                    if ($model = $this->setLesson($lesson_items, $dataItem['studyplan_subject_id'], 0, $timestamp_in)) {
                        $this->setProgress($model, $dataItem);
                    }
                }
            }
        }
    }

    protected function setProgress($model, $dataItem)
    {
        $model_th = new LessonProgress();
        $model_th->lesson_items_id = $model->id;
        $model_th->studyplan_subject_id = $dataItem['studyplan_subject_id'];
       // $model_th->lesson_mark_id = 1017;
        return $model_th->save(false);
    }

    protected function setLesson($lesson_items, $studyplan_subject_id, $subject_sect_studyplan_id, $timestamp_in)
    {
        if (!isset($lesson_items[$subject_sect_studyplan_id][$studyplan_subject_id])) {
            $model = new LessonItems();
            $model->subject_sect_studyplan_id = $subject_sect_studyplan_id;
            $model->studyplan_subject_id = $studyplan_subject_id;
            $model->lesson_test_id = 1000;
            $model->lesson_date = Yii::$app->formatter->asDate($timestamp_in, 'php:d.m.Y');
            return $model->save(false) ? $model : false;
        }
        return false;
    }
}
