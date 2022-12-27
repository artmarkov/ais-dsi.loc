<?php

namespace backend\controllers\education;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Образовательные программы',  'url' => ['/education/default/index']],
        ['label' => 'Группы образовательных программ',  'url' => ['/education/education-union/index']],

    ];
}