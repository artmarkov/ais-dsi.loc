<?php

namespace backend\controllers\teachers;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Преподаватели',  'url' => ['/teachers/default/index']],
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