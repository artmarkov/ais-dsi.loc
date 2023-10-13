<?php

namespace frontend\controllers\parents;

use artsoft\models\User;
use Yii;
use artsoft\helpers\RefBook;
use yii\web\ForbiddenHttpException;

class MainController extends \frontend\controllers\DefaultController
{
    public $parents_id;

    public function init()
    {
        $this->viewPath = '@backend/views/parents/default';

        if(!User::hasRole(['parents'])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $userId = Yii::$app->user->identity->getId();
        $this->parents_id = RefBook::find('users_parents')->getValue($userId) ?? null;
        parent::init();
    }

}