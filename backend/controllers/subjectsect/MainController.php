<?php

namespace backend\controllers\subjectsect;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Учебные группы',  'url' => ['/subjectsect/default/index']],
    ];
}