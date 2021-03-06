<?php

namespace backend\controllers\subject;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Дисциплины школы',  'url' => ['/subject/default/index']],
        ['label' => 'Раздел дисциплины',  'url' => ['/subject/category/index']],
        ['label' => 'Тип занятий',  'url' => ['/subject/type/index']],
        ['label' => 'Форма занятий',  'url' => ['/subject/vid/index']],
    ];
}