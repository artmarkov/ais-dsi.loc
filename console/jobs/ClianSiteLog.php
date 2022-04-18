<?php

namespace console\jobs;

use artsoft\models\UserVisitLog;
use Yii;

/**
 * Class ClianSiteLog.
 */
class ClianSiteLog extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
       $shelf_life_sitelog = Yii::$app->settings->get('module.shelf_life_sitelog', 180);
       $timestamp = time() - $shelf_life_sitelog * 86400;
        UserVisitLog::deleteAll(['<', 'visit_time', $timestamp]);
    }

}
