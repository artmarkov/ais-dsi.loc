<?php

namespace backend\controllers\schedule;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Расписание занятий',  'url' => ['/schedule/default/index']],
    ];

}