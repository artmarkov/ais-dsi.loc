<?php

namespace backend\controllers\student;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Ученики школы',  'url' => ['/student/default/index']],
        ['label' => 'Статус учащегося',  'url' => ['/student/position/index']],
    ];
}