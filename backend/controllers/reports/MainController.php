<?php

namespace backend\controllers\reports;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Табель учета пед.часов', 'url' => ['/reports/default/index']],
    ];
}