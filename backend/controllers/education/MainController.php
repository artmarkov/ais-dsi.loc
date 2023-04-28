<?php

namespace backend\controllers\education;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Образовательные программы',  'url' => ['/education/default/index']],
        ['label' => 'Программы для предварительной записи',  'url' => ['/education/entrant-programm/index']],

    ];
}