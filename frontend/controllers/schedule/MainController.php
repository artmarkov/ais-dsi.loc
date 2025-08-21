<?php

namespace frontend\controllers\schedule;

use artsoft\models\User;
use Yii;
use yii\web\ForbiddenHttpException;

class MainController extends \frontend\controllers\DefaultController
{

    public function init()
    {
        $this->viewPath = '@backend/views/schedule/default';

        if(!User::hasRole(['employees', 'teacher', 'department'/*, 'student', 'parents'*/])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        parent::init();
    }

}