<?php

namespace backend\controllers\studyplan;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Планы учащихся',  'url' => ['/studyplan/default/index']],
    ];
}