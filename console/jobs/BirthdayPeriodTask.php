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
        $models = $this->getDatesArray(null, date('m', time()), date('Y', time()));
        $mails_array = explode(',', Yii::$app->settings->get('mailing.mailing_birthday_period', ''));
        $mails_array = array_map('trim', $mails_array);
        array_unshift($mails_array, Yii::$app->params['adminEmail']);

        if ($models) {
            $textBody = 'Дни рождения у сотрудников на месяц ' . date('m.Y', time()) . PHP_EOL;
            $htmlBody = '<p><b>Дни рождения у сотрудников на месяц</b> ' . date('m.Y', time()) . '</p>';
            foreach ($models as $item => $model) {
                $age = ArtHelper::age($model['birth_date']);
                $textBody .= strip_tags($model['category_name']) . ': ' . strip_tags($model['fullname']) . ' - ' . date('d.m.Y', $model['birth_date']) . ' (' . $age['age_year'] . ' ' . ArtHelper::per($age['age_year']) . ')' . PHP_EOL;
                $htmlBody .= '<p>' . strip_tags($model['category_name']) . ': ' . strip_tags($model['fullname']) . ' - ' . date('d.m.Y', $model['birth_date']) . ' (' . $age['age_year'] . ' ' . ArtHelper::per($age['age_year']) . ')</p>';
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
        //инициализируем массив, в котором будем сохранять даты
        $models = array();
        //определяем день старта
        if ($day) {
            $date = $year . '-' . $month . '-' . $day;
        } else {
            $date = $year . '-' . $month . '-01';
        }
//заполняем массив датами
        do {
            $models = array_merge($models, UserCommon::getUsersBirthdayByCategory(['employees', 'teachers'], strtotime($date)));
            $date = date('Y-m-d', strtotime($date . ' + 1 days'));
            $currDateArr = explode('-', $date);
        } while ($month == $currDateArr[1]);

        return $models;
    }

}
