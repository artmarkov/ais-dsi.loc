<?php

namespace backend\controllers\routine;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'События',  'url' => ['/routine/default/index']],
        ['label' => 'Календарь',  'url' => ['/routine/default/calendar']],
        ['label' => 'Категории событий',  'url' => ['/routine/routine-cat/index']],
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