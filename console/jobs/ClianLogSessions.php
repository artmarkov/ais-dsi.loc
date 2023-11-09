<?php

namespace console\jobs;

use artsoft\models\Session;

/**
 * Class ClianLogSessions.
 */
class ClianLogSessions extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        Session::deleteAll(['is', 'user_id', null]);
        Session::deleteAll(['<', 'expire', time()]);
    }

}
