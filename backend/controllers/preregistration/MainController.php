<?php

namespace backend\controllers\preregistration;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Предварительная запись',  'url' => ['/preregistration/default/index']],
//        ['label' => 'Результаты',  'url' => ['/preregistration/summary/index']],

    ];
}