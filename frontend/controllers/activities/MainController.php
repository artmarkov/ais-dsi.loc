<?php

namespace frontend\controllers\activities;

use artsoft\models\User;
use Yii;
use yii\web\ForbiddenHttpException;

class MainController extends \frontend\controllers\DefaultController
{
    public function init()
    {

        if(!User::hasRole(['student','teacher','department'])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        parent::init();
    }

    public $tabMenu = [
        ['label' => 'Внешние мероприятия',  'url' => ['/activities/schoolplan-outside/index']],
        ['label' => 'Календарь мероприятий',  'url' => ['/activities/default/calendar']],
        ['label' => 'Ежедневник по аудиториям',  'url' => ['/activities/auditory-schedule/index']],
        ['label' => 'Ежедневник по преподавателям',  'url' => ['/activities/teachers-schedule/index']],
    ];
}