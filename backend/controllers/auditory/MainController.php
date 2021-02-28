<?php

namespace backend\controllers\auditory;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Аудитории школы',  'url' => ['/auditory/default/index']],
        ['label' => 'Категории',  'url' => ['/auditory/cat/index']],
        ['label' => 'Здания',  'url' => ['/auditory/building/index']],
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