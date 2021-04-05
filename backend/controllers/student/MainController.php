<?php

namespace backend\controllers\student;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Ученики школы',  'url' => ['/student/default/index']],
        ['label' => 'Состояние учащегося',  'url' => ['/student/position/index']],
    ];
}