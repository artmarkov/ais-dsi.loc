<?php

namespace backend\controllers\own;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Банковские реквизиты',  'url' => ['/own/default/index']],
        ['label' => 'Отделения',  'url' => ['/own/division/index']],
        ['label' => 'Отделы',  'url' => ['/own/department/index']],
    ];
}