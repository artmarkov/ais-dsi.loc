<?php

namespace backend\controllers\students;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Ученики школы',  'url' => ['/students/default/index']],
        ['label' => 'Состояние учащегося',  'url' => ['/students/position/index']],
    ];
}