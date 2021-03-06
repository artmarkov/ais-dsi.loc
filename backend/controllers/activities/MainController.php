<?php

namespace backend\controllers\activities;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Список мероприятий',  'url' => ['/activities/default/index']],
        ['label' => 'Календарь мероприятий',  'url' => ['/activities/default/calendar']],
        ['label' => 'Календарь по аудиториям',  'url' => ['/activities/schedule/index']],
        ['label' => 'Категории мероприятий',  'url' => ['/activities/activities-cat/index']],
    ];
}