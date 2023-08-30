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
        ['label' => '������� �����������',  'url' => ['/activities/schoolplan-outside']],
        ['label' => '��������� �����������',  'url' => ['/activities/default/calendar']],
        ['label' => '���������� �� ����������',  'url' => ['/activities/auditory-schedule/index']],
        ['label' => '���������� �� ��������������',  'url' => ['/activities/teachers-schedule/index']],
    ];
}