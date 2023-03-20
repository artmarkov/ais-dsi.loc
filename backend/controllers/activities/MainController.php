<?php

namespace backend\controllers\activities;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Список мероприятий',  'url' => ['/activities/default/index']],
        ['label' => 'Внеплановые мероприятия',  'url' => ['/activities/activities-over/index']],
        ['label' => 'Внешние мероприятия',  'url' => ['/activities/schoolplan-outside']],
        ['label' => 'Календарь мероприятий',  'url' => ['/activities/default/calendar']],
        ['label' => 'Ежедневник по аудиториям',  'url' => ['/activities/auditory-schedule/index']],
        ['label' => 'Ежедневник по преподавателям',  'url' => ['/activities/teachers-schedule/index']],
    ];
}