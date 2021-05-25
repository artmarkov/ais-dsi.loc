<?php

namespace backend\controllers\efficiency;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Показатели эффективности',  'url' => ['/efficiency/default/index']],
        ['label' => 'Сводный отчет',  'url' => ['/efficiency/default/summary']],
        ['label' => 'Дерево показателей',  'url' => ['/efficiency/efficiency-tree/index']],
    ];
}