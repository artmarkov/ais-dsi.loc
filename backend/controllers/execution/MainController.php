<?php

namespace backend\controllers\execution;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Расписания на подписи', 'url' => ['/execution/default/index']],
        ['label' => 'Расписания консультаций на подписи', 'url' => ['/execution/default/consult']],
        ['label' => 'Контроль заполнения планов', 'url' => ['/execution/default/thematic']],
        ['label' => 'Контроль заполнения журналов успеваемости', 'url' => ['/execution/default/progress']],
    ];
}