<?php

namespace backend\controllers\guidestudy;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Состояние учащегося',  'url' => ['/guidestudy/default/index']],
        ['label' => 'Образовательные программы',  'url' => ['/guidestudy/education-cat/index']],
        ['label' => 'Специализации',  'url' => ['/guidestudy/education-speciality/index']],
        ['label' => 'Образовательный уровень',  'url' => ['/guidestudy/education-level/index']],
        ['label' => 'Объединения учебных программ',  'url' => ['/guidestudy/education-union/index']],
    ];
}