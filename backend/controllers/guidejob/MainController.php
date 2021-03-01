<?php

namespace backend\controllers\guidejob;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Вид работы',  'url' => ['/guidejob/default/index']],
        ['label' => 'Направление деятельности',  'url' => ['/guidejob/direction/index']],
        ['label' => 'Образование',  'url' => ['/guidejob/level/index']],
        ['label' => 'Должность',  'url' => ['/guidejob/position/index']],
        ['label' => 'Ставки',  'url' => ['/guidejob/stake/index']],
        ['label' => 'Значение ставки',  'url' => ['/guidejob/cost/index']],
        ['label' => 'Достижения',  'url' => ['/guidejob/bonus-item/index']],
        ['label' => 'Категории достижений',  'url' => ['/guidejob/bonus-category/index']],
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