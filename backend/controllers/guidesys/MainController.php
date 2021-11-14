<?php

namespace backend\controllers\guidesys;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Отношения пользователей',  'url' => ['/guidesys/default/index']],
        ['label' => 'Категории мероприятий',  'url' => ['/guidesys/activities-cat/index']],
        ['label' => 'Категории событий',  'url' => ['/guidesys/routine-cat/index']],
        ['label' => 'Дерево показателей',  'url' => ['/guidesys/efficiency-tree/index']],
        ['label' => 'Руководство пользователя',  'url' => ['/guidesys/guide-help/index']],
    ];
}