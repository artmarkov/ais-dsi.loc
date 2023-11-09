<?php

namespace console\jobs;

use artsoft\helpers\ArtHelper;
use common\models\user\UserCommon;
use Yii;

/**
 * Class BirthdayPeriodTask.
 */
class BirthdayPeriodTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $timestamp = strtotime('first day of +1 month'); // следующий месяц независимо от текущей даты
        $month = date('m', $timestamp);
        $year = date('Y', $timestamp);
        $models = $this->getDatesArray(null, $month, $year);
        $mails_array = [Yii::$app->params['adminEmail']];

        if (Yii::$app->settings->get('mailing.mailing_birthday_period') != '') {
            $mails = explode(',', Yii::$app->settings->get('mailing.mailing_birthday_period'));
            $mails = array_map('trim', $mails);
            $mails_array = array_merge($mails_array, $mails);
        }

        if ($models) {
            $textBody = 'Дни рождения у сотрудников на месяц ' . date('m.Y', $timestamp) . PHP_EOL;
            $htmlBody = '<p><b>Дни рождения у сотрудников на месяц</b> ' . date('m.Y', $timestamp) . '</p>';
            foreach ($models as $time => $modelsForDay) {
                foreach ($modelsForDay as $item => $model) {
                    $age = ArtHelper::age($model['birth_date'], $time);
                    $textBody .= strip_tags($model['category_name']) . ': ' . strip_tags($model['fullname']) . ' - ' . date('d.m.Y', $model['birth_date']) . ' (' . $age['age_year'] . ' ' . ArtHelper::per($age['age_year']) . ')' . PHP_EOL;
                    $htmlBody .= '<p>' . strip_tags($model['category_name']) . ': ' . strip_tags($model['fullname']) . ' - ' . date('d.m.Y', $model['birth_date']) . ' (' . $age['age_year'] . ' ' . ArtHelper::per($age['age_year']) . ')</p>';
                }
            }
            $textBody .= '--------------------------' . PHP_EOL;
            $textBody .= 'Сообщение создано автоматически. Отвечать на него не нужно.';
            $htmlBody .= '<hr>';
            $htmlBody .= '<p>Сообщение создано автоматически. Отвечать на него не нужно.</p>';

            return Yii::$app->mailqueue->compose()
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($mails_array)
                ->setSubject('Сообщение с сайта ' . Yii::$app->name)
                ->setTextBody($textBody)
                ->setHtmlBody($htmlBody)
                ->queue();
        }
    }

    protected function getDatesArray($day = null, $month, $year)
    {
        $models = [];
        if ($day) {
            $date = $year . '-' . $month . '-' . $day;
        } else {
            $date = $year . '-' . $month . '-01';
        }
        do {
            $timestamp = strtotime($date);
            $models[$timestamp] = UserCommon::getUsersBirthdayByCategory(['employees', 'teachers'], $timestamp);
            $date = date('Y-m-d', strtotime($date . ' + 1 days'));
            $currDateArr = explode('-', $date);
        } while ($month == $currDateArr[1]);

        return $models;
    }

}
