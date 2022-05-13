<?php

namespace console\jobs;

use Yii;

/**
 * Class MailQueue.
 */
class MailQueue extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        Yii::$app->mailqueue->process();
    }

}
