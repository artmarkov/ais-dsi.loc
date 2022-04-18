<?php

namespace console\jobs;

use artsoft\models\Request;
use Yii;

/**
 * Class ClianRequestLog.
 */
class ClianRequestLog extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
       $shelf_life_requestlog = Yii::$app->settings->get('module.shelf_life_requestlog', 180);
       $datetime = Yii::$app->formatter->asDatetime((time() - $shelf_life_requestlog * 86400), 'php:Y-m-d H:i:s');
        Request::deleteAll(['<', 'created_at', $datetime]);
    }

}
