<?php

namespace frontend\controllers\teachers;

use artsoft\models\User;
use Yii;
use artsoft\helpers\RefBook;
use yii\web\ForbiddenHttpException;

class MainController extends \frontend\controllers\DefaultController
{
    public $teachers_id;

    public function init()
    {
        $this->viewPath = '@backend/views/teachers/default';

        if(!User::hasRole(['teacher','department'])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $userId = Yii::$app->user->identity->getId();
        $this->teachers_id = RefBook::find('users_teachers')->getValue($userId) ?? null;
        parent::init();
    }

}