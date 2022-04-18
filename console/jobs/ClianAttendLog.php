<?php

namespace console\jobs;

use common\models\service\UsersAttendlog;
use Yii;

/**
 * Class ClianAttendLog.
 */
class ClianAttendLog extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
       $shelf_life_attendlog = Yii::$app->settings->get('module.shelf_life_attendlog', 180);
       $timestamp = time() - $shelf_life_attendlog * 86400;
        UsersAttendlog::deleteAll(['<', 'timestamp', $timestamp]);
    }

}
