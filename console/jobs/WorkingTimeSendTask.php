<?php

namespace console\jobs;

use Yii;
use common\models\service\WorkingTimeLog;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Отправка сообщений о посещаемости - отправл¤ет письма с информацией о посещаемости на основании информации из лога посещаемости(формируется задачей WorkingTimeLogTask) за рабочий день
 * Class WorkingTimeSendTask.
 */
class WorkingTimeSendTask extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {

    }

}
