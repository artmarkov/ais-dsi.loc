<?php

namespace backend\controllers\guidejob;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Вид работы',  'url' => ['/guidejob/default/index']],
        ['label' => 'Вид деятельности',  'url' => ['/guidejob/direction-vid/index']],
        ['label' => 'Направление деятельности',  'url' => ['/guidejob/direction/index']],
        ['label' => 'Образование',  'url' => ['/guidejob/level/index']],
        ['label' => 'Должность',  'url' => ['/guidejob/position/index']],
        ['label' => 'Ставки',  'url' => ['/guidejob/stake/index']],
        ['label' => 'Значение ставки',  'url' => ['/guidejob/cost/index']],
        ['label' => 'Достижения',  'url' => ['/guidejob/bonus/index']],
        ['label' => 'Категории достижений',  'url' => ['/guidejob/bonus-category/index']],
    ];
}