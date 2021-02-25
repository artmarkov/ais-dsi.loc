<?php

/**
 * %progdir%\modules\php\%phpdriver%\php-win.exe -c %progdir%\modules\php\%phpdriver%\php.ini -q -f %sitedir%\artsoft.loc\yii schedule
 */

namespace console\controllers;

use yii\console\Controller;
use Yii;

class ScheduleController extends Controller {

    public function actionIndex()
    {
        Yii::$app->queue->push(new \artsoft\queue\jobs\TestJob([
            'text' => "test job run " . date('d.m.Y H:i:s', time()) . " \n",
            'file' => __DIR__ . '/test.txt',
        ]));
        Yii::$app->queue->push(new \artsoft\mailbox\jobs\MessageNewEmailJob());
        Yii::$app->queue->push(new \artsoft\mailbox\jobs\ClianDeletedMailJob());
        Yii::$app->queue->push(new \artsoft\mailbox\jobs\TrashMailJob());

        $queue = Yii::$app->queue;
        $queue->run(false);
    }
}
