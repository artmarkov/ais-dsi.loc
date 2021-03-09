<?php

namespace backend\controllers\routine;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'События',  'url' => ['/routine/default/index']],
        ['label' => 'Календарь',  'url' => ['/routine/default/calendar']],
        ['label' => 'Категории событий',  'url' => ['/routine/routine-cat/index']],
    ];
}