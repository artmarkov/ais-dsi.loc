<?php

namespace backend\widgets\dashboard;

use artsoft\widgets\DashboardWidget;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Visits extends DashboardWidget
{
    public function run()
    {
        $day = date('d');
        $mon = date('m');
        $year = date('Y');

        $timestamp_in = \Yii::$app->formatter->asTimestamp(mktime(0, 0, 0, $mon, $day, $year));
        $timestamp_out = $timestamp_in + 86399;

        $active = (new Query())->from('activities_schedule_studyplan_view')
            ->select(new \yii\db\Expression('auditory.building_id, COUNT(DISTINCT student_id) AS count_student, COUNT(DISTINCT teachers_id) AS count_teachers'))
            ->innerJoin('auditory', 'auditory.id = activities_schedule_studyplan_view.auditory_id')
            ->where(['between', 'datetime_in', $timestamp_in, $timestamp_out])
            ->andWhere(['activities_schedule_studyplan_view.status' => 1])
            ->groupBy('auditory.building_id')
            ->all();
        $active = ArrayHelper::index($active,'building_id');

        return $this->render('visits', [
            'active' => $active,
            'date' => \Yii::$app->formatter->asDate($timestamp_in)
        ]);
    }
}