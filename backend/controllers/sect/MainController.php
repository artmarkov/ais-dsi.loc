<?php

namespace backend\controllers\sect;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Учебные группы',  'url' => ['/sect/default/index']],
    ];
}