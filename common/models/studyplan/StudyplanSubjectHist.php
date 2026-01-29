<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use common\models\schedule\SubjectScheduleView;
use Yii;
use yii\helpers\ArrayHelper;

class StudyplanSubjectHist extends StudyplanSubject
{
    /** Получаем статус дисциплины за указанный месяц для конкретной дисциплины
     * @param $studyplan_subject_id
     * @param bool $timestamp
     * @return array
     * @throws \yii\db\Exception
     */
    /* public static function getStatusHist($studyplan_subject_id, $timestamp = false)
     {
         $timestamp = $timestamp ? $timestamp : time();
         $timestamp = ArtHelper::getMonYearParamsForTimestamp($timestamp);

         $query = \Yii::$app->getDb()->createCommand('
             select status
             from studyplan_subject_hist
             where (updated_at BETWEEN :timestamp_in AND :timestamp_out)
             and studyplan_subject_id = :studyplan_subject_id
             and op != \'D\'
             order by hist_id desc limit 1',
             [
                 'timestamp_in' => $timestamp[0],
                 'timestamp_out' => $timestamp[1],
                 'studyplan_subject_id' => $studyplan_subject_id
             ])
             ->queryColumn();
         return $query;
     }*/

    /**
     * Получаем список дисциплин со статусом 0 за заданный месяц
     * @param bool $timestamp_in
     * @param bool $timestamp_out
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getStudyplanSubjectPass($timestamp = false)
    {
        $timestamp = $timestamp ? $timestamp : time();
        $timestamp = ArtHelper::getMonYearParamsForTimestamp($timestamp);
        $date_in = $timestamp[0];
        $date_out = $timestamp[1];

        $query = \Yii::$app->getDb()->createCommand('
            select id
            from studyplan_subject_hist h
            where (updated_at <= :timestamp) 
            and status = 0
            and version = (select MAX(version) from studyplan_subject_hist where id = h.id)
            and op != \'D\'
            ',
            [
                'timestamp' => $date_out,
            ])
            ->queryColumn();
        return $query;
    }

    public static function getScheduleItemsPass($timestamp = false)
    {
        $query = SubjectScheduleView::find()->select('subject_schedule_id')
            ->where(['studyplan_subject_id' => self::getStudyplanSubjectPass($timestamp)])
            ->column();

        return $query;
    }
}
