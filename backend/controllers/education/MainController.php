<?php

namespace backend\controllers\education;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Учебные программы',  'url' => ['/education/default/index']],
        ['label' => 'Образовательные программы',  'url' => ['/education/education-cat/index']],
        ['label' => 'Специализации',  'url' => ['/education/education-speciality/index']],
        ['label' => 'Образовательный уровень',  'url' => ['/education/education-level/index']],
    ];
}