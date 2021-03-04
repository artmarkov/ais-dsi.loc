<?php

namespace backend\controllers\activities;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Список мероприятий',  'url' => ['/activities/default/index']],
        ['label' => 'Календарь мероприятий',  'url' => ['/activities/default/calendar']],
        ['label' => 'Категории мероприятий',  'url' => ['/activities/activities-cat/index']],
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