<?php

namespace console\jobs;

use Yii;

/**
 * Class ClianMailQueueTask.
 */
class ClianMailQueueTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        Yii::$app->db->createCommand('TRUNCATE TABLE mail_queue')->execute();
    }

}
