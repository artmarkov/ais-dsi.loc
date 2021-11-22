<?php

namespace backend\controllers\studygroups;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Учебные группы',  'url' => ['/studygroups/default/index']],
    ];
}