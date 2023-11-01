<?php

namespace frontend\controllers\schoolplan;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use Yii;
use yii\web\ForbiddenHttpException;

class MainController extends \frontend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'План работы школы',  'url' => ['/schoolplan/default/index'],  'visible' => true],
    ];

    public $teachers_id;

    public function init()
    {
        $this->viewPath = '@backend/views/schoolplan/default';

        if(!User::hasRole(['teacher','department'])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $userId = Yii::$app->user->identity->getId();
        $this->teachers_id = RefBook::find('users_teachers')->getValue($userId) ?? null;
        parent::init();
    }

}