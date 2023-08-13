<?php

namespace frontend\controllers\teachers;

use Yii;
use artsoft\helpers\RefBook;

class MainController extends \frontend\controllers\DefaultController
{
    public $teachers_id;

    public function init()
    {
        $this->viewPath = '@backend/views/teachers/default';

        $userId = Yii::$app->user->identity->getId();
        $this->teachers_id = RefBook::find('users_teachers')->getValue($userId) ?? null;
        parent::init();
    }

}