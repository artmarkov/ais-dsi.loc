<?php

namespace backend\controllers\subject;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Учебные предметы',  'url' => ['/subject/default/index']],
        ['label' => 'Раздел учебных предметов',  'url' => ['/subject/category/index']],
        ['label' => 'Тип занятий',  'url' => ['/subject/type/index']],
        ['label' => 'Вид занятий',  'url' => ['/subject/vid/index']],
        ['label' => 'Форма обучения',  'url' => ['/subject/form/index']],
    ];
}