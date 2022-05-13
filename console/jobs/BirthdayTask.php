<?php

namespace console\jobs;

use artsoft\helpers\ArtHelper;
use common\models\user\UserCommon;
use Yii;

/**
 * Class BirthdayTask.
 */
class BirthdayTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $models = UserCommon::getUsersBirthdayByCategory(['employees', 'teachers']);
        if ($models) {
            $textBody = 'Дни рождения у сотрудников на сегодня ' . date('d.m.Y', time()) . PHP_EOL;
            $htmlBody = '<p><b>Дни рождения у сотрудников на сегодня</b> ' . date('d.m.Y', time()) . '</p>';
            foreach ($models as $item => $model) {
                $age = ArtHelper::age($model['birth_date']);
                $textBody .= strip_tags($model['category_name']) . ': ' . strip_tags($model['fullname']) . ' - ' . $age['age_year'] . ' ' . ArtHelper::per($age['age_year']) . PHP_EOL;
                $htmlBody .= '<p>' . strip_tags($model['category_name']) . ': ' . strip_tags($model['fullname']) . ' - ' . $age['age_year'] . ' ' . ArtHelper::per($age['age_year']) . '</p>';
            }
            $textBody .= '--------------------------' . PHP_EOL;
            $textBody .= 'Сообщение создано автоматически. Отвечать на него не нужно.';
            $htmlBody .= '<hr>';
            $htmlBody .= '<p>Сообщение создано автоматически. Отвечать на него не нужно.</p>';

            return Yii::$app->mailqueue->compose()
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject('Сообщение с сайта ' . Yii::$app->name)
                ->setTextBody($textBody)
                ->setHtmlBody($htmlBody)
                ->queue();
        }
    }

}
