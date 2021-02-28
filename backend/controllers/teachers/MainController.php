<?php

namespace backend\controllers\teachers;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Преподаватели',  'url' => ['/teachers/default/index']],
        ['label' => 'Направление деятельности',  'url' => ['/teachers/direction/index']],
        ['label' => 'Вид работы',  'url' => ['/teachers/work/index']],
        ['label' => 'Образование',  'url' => ['/teachers/level/index']],
        ['label' => 'Должности',  'url' => ['/teachers/position/index']],
        ['label' => 'Ставки',  'url' => ['/teachers/stake/index']],
        ['label' => 'Значение ставки',  'url' => ['/teachers/cost/index']],
        ['label' => 'Достижения',  'url' => ['/teachers/bonus-item/index']],
        ['label' => 'Категории достижений',  'url' => ['/teachers/bonus-category/index']],
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