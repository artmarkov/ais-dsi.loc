<?php

namespace console\jobs;

use common\models\service\UsersCardLog;
use Yii;

/**
 * Class ClianLogSigur.
 */
class ClianLogSigur extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
       $shelf_life_pass = Yii::$app->settings->get('module.shelf_life_pass', 180);
       $datetime = Yii::$app->formatter->asDatetime((time() - $shelf_life_pass * 86400), 'php:Y-m-d H:i:s');
        UsersCardLog::deleteAll(['<', 'datetime', $datetime]);
    }

}
