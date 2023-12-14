<?php

namespace backend\controllers\test;

use common\models\schoolplan\Schoolplan;
use common\models\schoolplan\SchoolplanPerform;
use Yii;

class MailboxController extends \backend\controllers\DefaultController
{
    public function actionIndex()
    {
       // $model = Schoolplan::findOne(149);
        $model = SchoolplanPerform::findOne(6);
        $receiverId = 1136;

        Yii::$app->mailbox->send($receiverId, $model, 'Прошу Вас отметить учащихся, принимающий участие...');
    }

}