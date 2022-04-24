<?php

namespace console\jobs;

/**
 * Class ClianDeletedMailJob.
 */
class ClianDeletedMailJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $modelClass = 'artsoft\mailbox\models\Mailbox';
    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $this->modelClass::clianDeletedMail($this->modelClass::getDeletedMail()); // удаляет все письма физически
    }
   
}
