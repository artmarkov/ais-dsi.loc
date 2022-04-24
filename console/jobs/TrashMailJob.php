<?php

namespace console\jobs;

/**
 * Class TrashMailJob.
 */
class TrashMailJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $modelClass = 'artsoft\mailbox\models\Mailbox';
    public $modelViaClass = 'artsoft\mailbox\models\MailboxInbox';
    
    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $id = $this->modelClass::getTrashMail();
        if (!empty($id)) {

            $where = ['id' => $id, 'status_del' => $this->modelClass::STATUS_DEL_TRASH];
            $this->modelClass::updateAll(['status_del' => $this->modelClass::STATUS_DEL_DELETE, 'deleted_at' => time()], $where);

            $whereVia = ['mailbox_id' => $id, 'status_del' => $this->modelClass::STATUS_DEL_TRASH];
            $this->modelViaClass::updateAll(['status_del' => $this->modelClass::STATUS_DEL_DELETE, 'deleted_at' => time()], $whereVia);
        }
    }   
}
