<?php

namespace backend\controllers\subject;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Дисциплины школы',  'url' => ['/subject/default/index']],
        ['label' => 'Категории дисциплин',  'url' => ['/subject/category-item/index']],
        ['label' => 'Типы дисциплин',  'url' => ['/subject/type/index']],
        ['label' => 'Виды дисциплин',  'url' => ['/subject/vid/index']],
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