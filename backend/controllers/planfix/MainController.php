<?php

namespace backend\controllers\planfix;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Планировщик задач',  'url' => ['/planfix/default/index']],
       // ['label' => 'Категория',  'url' => ['/creative/category/index']],
    ];
}