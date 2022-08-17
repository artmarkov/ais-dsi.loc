<?php

namespace backend\controllers\activities;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Список мероприятий',  'url' => ['/activities/default/index']],
        ['label' => 'Внеплановые мероприятия',  'url' => ['/activities/activities-over/index']],
        ['label' => 'Календарь мероприятий',  'url' => ['/activities/default/calendar']],
        ['label' => 'Календарь по аудиториям',  'url' => ['/activities/schedule/index']],
    ];
}