<?php

use artsoft\widgets\Nav;

?>
<!-- SIDEBAR NAV -->
<div class="navbar-default sidebar metismenu" role="navigation">
    <?= Nav::widget([
        'encodeLabels' => false,
        'dropDownCaret' => '<span class="arrow"></span>',
        'options' => [
            ['class' => 'nav side-menu'],
            ['class' => 'nav nav-second-level'],
            ['class' => 'nav nav-third-level']
        ],
        'items' => [
            [
                'label' => 'Главная',
                'icon' => 'fa fa-th',
                'url' => Yii::$app->urlManager->hostInfo,
            ],
            [
                'label' => 'Информационный ресурс',
                'icon' => 'fa fa-inbox',
                'url' => '#',
                'items' => [
                    ['label' => 'Каталог файлов', 'icon' => 'fa fa-minus', 'url' => ['/info/default/index']],
                    ['label' => 'Объявления', 'icon' => 'fa fa-minus', 'url' => ['/info/board/index']],
                    ['label' => 'Формы и заявки', 'icon' => 'fa fa-minus', 'url' => ['/info/forms/index'],
                    ],
                ],
            ],
            [
                'label' => 'Реестры',
                'icon' => 'fa fa-list',
                'url' => '#',
                'items' => [
                    ['label' => 'Список сотрудников', 'icon' => 'fa fa-minus', 'url' => ['/employees/default/index']],
                    ['label' => 'Список преподавателей', 'icon' => 'fa fa-minus', 'url' => ['/teachers/default/index']],
                    ['label' => 'Список учеников', 'icon' => 'fa fa-minus', 'url' => ['/students/default/index']],
                    ['label' => 'Список родителей', 'icon' => 'fa fa-minus', 'url' => ['/parents/default/index']],
                    ['label' => 'Заявления', 'icon' => 'fa fa-minus', 'url' => ['/order/default/index']],
                ],
            ],
            [
                'label' => 'Организационная работа',
                'icon' => 'fa fa-university',
                'url' => '#',
                'items' => [
                    ['label' => 'Учебные планы', 'icon' => 'fa fa-minus', 'url' => ['/education/default/index']],
                    ['label' => 'Счета за обучение', 'icon' => 'fa fa-minus', 'url' => ['/invoices/default/index']],
                    ['label' => 'Табель учета пед.часов', 'icon' => 'fa fa-minus', 'url' => ['/timesheet/default/index']],
                    ['label' => 'Производственный календарь', 'icon' => 'fa fa-minus', 'url' => ['/routine/default/index']],
                    ['label' => 'Работы и сертификаты', 'icon' => 'fa fa-minus', 'url' => ['/creative/default/index']],
                ],
            ],
            [
                'label' => 'Учебная работа',
                'icon' => 'fa fa-graduation-cap',
                'url' => '#',
                'items' => [
                    ['label' => 'Испытания', 'icon' => 'fa fa-minus', 'url' => ['/examination/default/index']],
                    ['label' => 'Индивидуальные планы', 'icon' => 'fa fa-minus', 'url' => ['/studyplan/default/index']],
                    ['label' => 'Движение учеников', 'icon' => 'fa fa-minus', 'url' => ['/transfer/default/index']],
                    ['label' => 'Расписание занятий', 'icon' => 'fa fa-minus', 'url' => ['/schedule/default/index']],
                    ['label' => 'Расписание консультаций', 'icon' => 'fa fa-minus', 'url' => ['/consult/default/index']],
                    ['label' => 'Календарь мероприятий', 'icon' => 'fa fa-minus', 'url' => ['/activities/default/index']],
                    ['label' => 'Журнал успеваемости', 'icon' => 'fa fa-minus', 'url' => ['/progress/default/index']],
                    ['label' => 'Учебные группы', 'icon' => 'fa fa-minus', 'url' => ['/studygroups/default/index']],
                ],
            ],
            [
                'label' => 'Аналитика',
                'icon' => 'fa fa-bar-chart',
                'url' => '#',
                'items' => [
                    ['label' => 'Отчеты', 'icon' => 'fa fa-minus', 'url' => ['/reports/default/index']],
                    ['label' => 'Журнал посещений', 'icon' => 'fa fa-minus', 'url' => ['/attandlog/default/index']],
                    ['label' => 'Показатели эфективности', 'icon' => 'fa fa-minus', 'url' => ['/efficiency/default/index']],
                    ['label' => 'Портфолио преподавателей', 'icon' => 'fa fa-minus', 'url' => ['/portfolio/default/index']],
                    ['label' => 'Контроль исполнения', 'icon' => 'fa fa-minus', 'url' => ['/execution/default/index']],
                    ['label' => 'Сводная успеваемость', 'icon' => 'fa fa-minus', 'url' => ['/summary-progress/default/index']],
                ],
            ],
            [
                'label' => 'Помощь',
                'icon' => 'fa fa-question-circle',
                'url' => '#',
                'items' => [
                    ['label' => 'Техническая поддержка', 'icon' => 'fa fa-minus', 'url' => ['/help/support/index']],
                    ['label' => 'Руководство пользователя', 'icon' => 'fa fa-minus', 'url' => ['/help/guide-help/index']],
                    ['label' => 'О системе', 'icon' => 'fa fa-minus', 'url' => ['/help/support/about']],
                ],
            ],
        ],
    ]) ?>
</div>
<!-- !SIDEBAR NAV -->