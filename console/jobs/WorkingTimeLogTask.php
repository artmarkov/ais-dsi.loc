<?php

namespace console\jobs;

use common\models\auditory\Auditory;
use Yii;
use common\models\service\WorkingTimeLog;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Записывает в лог посещаемости время начала и окончания работы по расписанию и фактическое время прохода(полученя ключей) за рабочий день
 * Class WorkingTimeLogTask.
 */
class WorkingTimeLogTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $resource = [/*'schoolplan',*/ 'consult_schedule', /*'activities_over',*/ 'subject_schedule']; // выборка по Плану работы, Консультациям, Внеплановая работа, Расписание занятий
        $start_time = strtotime("today"); // Дата сегодня в полноч
        $end_time = strtotime("tomorrow", $start_time) - 1; // Дата сегодня в 23.59.59
       /* $start_time = strtotime("yesterday");
        $end_time = $start_time +86300;*/
        $date = Yii::$app->formatter->asDate($start_time, 'php:Y-m-d'); // Дата в формате Y-m-d
        $auditories = Auditory::find()->select('id')->where(['building_id' => 1000])->column(); // Берем только аудитории в Основном здании

        // Получаем все события за сутки
        $events = (new Query())->from('activities_teachers_view')
            ->select(new \yii\db\Expression('teachers_id, MIN(start_time) as start_time, MAX(end_time) as end_time'))
            ->where("start_time > :start_time and end_time < :end_time", [":start_time" => $start_time, ":end_time" => $end_time])
            ->andWhere(['resource' => $resource])
            ->andWhere(['auditory_id' => $auditories])
            ->groupBy('teachers_id')
            ->all();
        $teachersIds = ArrayHelper::getColumn($events, 'teachers_id');

        // Преподакатели исходя из событий
        $teachers = (new Query())->from('teachers_view')
            ->where(['teachers_id' => $teachersIds])
            ->all();
        $usersIds = ArrayHelper::getColumn($teachers, 'user_common_id');
        $users = ArrayHelper::map($teachers, 'teachers_id', 'user_common_id');

        // Выборка посещений за текущий день на основании журнала выдачи ключей
        $attendLog = (new Query())->from('users_attendlog_view')
            ->select(new \yii\db\Expression('user_common_id, MIN(timestamp_received) as start_time, MAX(timestamp_over) as end_time'))
            ->where(['user_common_id' => $usersIds])
            ->andWhere(['timestamp' => $start_time])
            ->groupBy('user_common_id')
            ->all();
        $attendLog = ArrayHelper::index($attendLog, 'user_common_id');

        // Формируем лог посещений
        foreach ($events as $item => $event) {
            $user_common_id = $users[$event['teachers_id']] ?? false;
            if(!$user_common_id) continue;

            $model = WorkingTimeLog::find()->where(['user_common_id' => $user_common_id])->andWhere(['date' => $date])->one() ?? new WorkingTimeLog();
            $model->user_common_id = $user_common_id;
            $model->date = $date;
            $model->timestamp_work_in = $attendLog[$user_common_id]['start_time'] ?? null;
            $model->timestamp_work_out = $attendLog[$user_common_id]['end_time'] ?? null;
            $model->timestamp_activities_in = $event['start_time'] ?? null;
            $model->timestamp_activities_out = $event['end_time'] ?? null;
            $model->save();
        }
    }

}
