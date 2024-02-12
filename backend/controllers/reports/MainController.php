<?php

namespace backend\controllers\reports;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Табель учета пед.часов', 'url' => ['/reports/default/index']],
        ['label' => 'Тарификационная ведомость', 'url' => ['/reports/default/tarif-statement']],
        ['label' => 'Контингент учащихся', 'url' => ['/reports/default/studyplan-stat']],
        ['label' => 'Расписание преподавателя', 'url' => ['/reports/default/teachers-schedule']],
        ['label' => 'График работы преподавателей', 'url' => ['/reports/default/generator-schedule']],
    ];
}