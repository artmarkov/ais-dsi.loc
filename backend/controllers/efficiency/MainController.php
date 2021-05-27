<?php

namespace backend\controllers\efficiency;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Показатели эффективности',  'url' => ['/efficiency/default/index']],
        ['label' => 'Сводная таблица',  'url' => ['/efficiency/default/summary']],
//        ['label' => 'Гистограмма',  'url' => ['/efficiency/default/histogram']],
        ['label' => 'Дерево показателей',  'url' => ['/efficiency/efficiency-tree/index']],
    ];
}