<?php

namespace backend\controllers\employees;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Сотрудники',  'url' => ['/employees/default/index']],
    ];
}