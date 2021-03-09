<?php

namespace backend\controllers\teachers;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Преподаватели',  'url' => ['/teachers/default/index']],
    ];
}