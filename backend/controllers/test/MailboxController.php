<?php

namespace backend\controllers\test;

use artsoft\mailbox\models\Mailbox;
use artsoft\mailbox\models\MailboxInbox;
use Yii;

class MailboxController extends \backend\controllers\DefaultController
{
    public function actionIndex()
    {

        $models = MailboxInbox::find()
            ->joinWith(['mailbox'])
            ->joinWith(['receiver'])
            ->select(['receiver_id', 'COUNT(*) AS qty'])
            ->where(['status_read' => Mailbox::STATUS_READ_NEW])
            ->andWhere(['status_post' => Mailbox::STATUS_POST_SENT])
            ->andWhere(['mailbox_inbox.status_del' => Mailbox::STATUS_DEL_NO])
            ->andWhere(['status' => \artsoft\models\User::STATUS_ACTIVE])
            ->groupBy('receiver_id')
            ->asArray()
            ->all();

        echo '<pre>' . print_r($models, true) . '</pre>';
       // Yii::$app->mailbox->send([10117, 91117],'Title Тест','Content Тест');
    }

}