<?php

namespace console\jobs;

use artsoft\models\Session;
use Yii;

/**
 * Class FlushCache.
 */
class FlushCache extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        Yii::$app->cache->flush();
    }

}
