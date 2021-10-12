<?php

namespace backend\controllers\guidesys;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Отношения пользователей',  'url' => ['/guidesys/default/index']],
        ['label' => 'Состояние учащегося',  'url' => ['/guidesys/position/index']],
        ['label' => 'Образовательные программы',  'url' => ['/guidesys/education-cat/index']],
        ['label' => 'Специализации',  'url' => ['/guidesys/education-speciality/index']],
        ['label' => 'Образовательный уровень',  'url' => ['/guidesys/education-level/index']],
        ['label' => 'Категории мероприятий',  'url' => ['/guidesys/activities-cat/index']],
        ['label' => 'Категории событий',  'url' => ['/guidesys/routine-cat/index']],
        ['label' => 'Дерево показателей',  'url' => ['/guidesys/efficiency-tree/index']],
    ];
}