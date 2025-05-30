<?php

namespace backend\controllers\reports;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Табель учета пед.часов', 'url' => ['/reports/default/index']],
        ['label' => 'Тарификационная ведомость', 'url' => ['/reports/default/tarif-statement']],
        ['label' => 'Контингент учащихся', 'url' => ['/reports/default/studyplan-stat']],
        ['label' => 'Форма №1', 'url' => ['/reports/default/studyplan-distrib'], 'visible' => true],
        ['label' => 'Загруженность учреждения', 'url' => ['/reports/default/school-workload'], 'visible' => true],
        ['label' => 'Резерв учебного времени аудиторий', 'url' => ['/reports/default/time-reserve'], 'visible' => true],
        ['label' => 'Выписка из расписания занятий', 'url' => ['/reports/default/teachers-schedule']],
        ['label' => 'Выписка из расписания консультаций', 'url' => ['/reports/default/teachers-consult']],
        ['label' => 'График работы преподавателей', 'url' => ['/reports/default/generator-schedule']],
        ['label' => 'Выписка из учебного плана', 'url' => ['/reports/default/student-history']],
        ['label' => 'Выписка из журнала успеваемости', 'url' => ['/reports/default/progress-history'], 'visible' => true],
    ];
}