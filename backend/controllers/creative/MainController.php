<?php

namespace backend\controllers\creative;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Творческие работы и сертификаты',  'url' => ['/creative/default/index']],
        ['label' => 'Категория',  'url' => ['/creative/category/index']],
    ];
}