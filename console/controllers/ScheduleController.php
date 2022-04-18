<?php

/**
 * %progdir%\modules\php\%phpdriver%\php-win.exe -c %progdir%\modules\php\%phpdriver%\php.ini -q -f %sitedir%\artsoft.loc\yii schedule
 */

namespace console\controllers;

use artsoft\queue\models\QueueSchedule;
use yii\console\Controller;
use Yii;

class ScheduleController extends Controller {

    public function actionIndex()
    {
        foreach (QueueSchedule::find()->all() as $task) {
            Yii::$app->queue->push(new $task->class);
        }
        $queue = Yii::$app->queue;
        $queue->run(false);
    }
}
