<?php

namespace backend\controllers\student;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Ученики школы',  'url' => ['/student/default/index']],
        ['label' => 'Статус учащегося',  'url' => ['/student/position/index']],
    ];

    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            case 'create':
                return ['update', 'id' => $model->id];
                break;
            default:
                return parent::getRedirectPage($action, $model);
        }
    }
}