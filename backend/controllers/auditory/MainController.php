<?php

namespace backend\controllers\auditory;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Аудитории школы',  'url' => ['/auditory/default/index']],
        ['label' => 'Категории',  'url' => ['/auditory/cat/index']],
        ['label' => 'Здания',  'url' => ['/auditory/building/index']],
    ];
}