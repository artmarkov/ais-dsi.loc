<?php

namespace frontend\controllers\schoolplan;

class MainController extends \frontend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'План работы школы',  'url' => ['/schoolplan/default/index'],  'visible' => true],
    ];
}