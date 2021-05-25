<?php

namespace backend\controllers\efficiency;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Сводная таблица',  'url' => ['/efficiency/default/summary']],
        ['label' => 'Гистограмма',  'url' => ['/efficiency/default/histogram']],
        ['label' => 'Элементы показателей',  'url' => ['/efficiency/default/index']],
        ['label' => 'Дерево показателей',  'url' => ['/efficiency/efficiency-tree/index']],
    ];
}