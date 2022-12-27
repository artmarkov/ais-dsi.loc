<?php

namespace backend\controllers\guidestudy;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Состояние учащегося',  'url' => ['/guidestudy/default/index']],
        ['label' => 'Виды образовательных программ',  'url' => ['/guidestudy/education-cat/index']],
        ['label' => 'Образовательный уровень',  'url' => ['/guidestudy/education-level/index']],
        ['label' => 'Категории произведений',  'url' => ['/guidestudy/piece-category/index']],
        ['label' => 'Оценки уроков',  'url' => ['/guidestudy/lesson-mark/index']],
        ['label' => 'Виды испытаний',  'url' => ['/guidestudy/lesson-test/index']],
        ['label' => 'Приемные испытания',  'url' => ['/guidestudy/entrant-test/index']],
    ];
}