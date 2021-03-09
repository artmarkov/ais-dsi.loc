<?php

namespace backend\controllers\activities;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Список мероприятий',  'url' => ['/activities/default/index']],
        ['label' => 'Календарь мероприятий',  'url' => ['/activities/default/calendar']],
        ['label' => 'Категории мероприятий',  'url' => ['/activities/activities-cat/index']],
    ];
}