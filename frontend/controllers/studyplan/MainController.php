<?php

namespace frontend\controllers\studyplan;

use artsoft\models\User;
use Yii;
use artsoft\helpers\RefBook;
use yii\web\ForbiddenHttpException;

class MainController extends \frontend\controllers\DefaultController
{
    public $student_id;

    public function init()
    {
        $this->viewPath = '@backend/views/studyplan/default';

        if(!User::hasRole(['student'])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $userId = Yii::$app->user->identity->getId();
        $this->student_id = RefBook::find('users_students')->getValue($userId) ?? null;
        parent::init();
    }

}