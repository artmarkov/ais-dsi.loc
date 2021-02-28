<?php

namespace backend\controllers\own;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Основные сведения',  'url' => ['/own/default/index']],
        ['label' => 'Отделения',  'url' => ['/own/division/index']],
        ['label' => 'Отделы',  'url' => ['/own/department/index']],
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