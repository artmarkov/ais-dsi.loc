<?php

namespace backend\controllers\schoolplan;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'План работы школы',  'url' => ['/schoolplan/default/index']],
    ];
}