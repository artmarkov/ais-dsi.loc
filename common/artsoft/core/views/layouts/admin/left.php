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
                'url' => Yii::$app->homeUrl,
                'visible' => \artsoft\models\User::hasPermission('viewDashboard')
            ],
            [
                'label' => 'Информационный ресурс',
                'icon' => 'fa fa-inbox',
                'url' => '#',
                'items' => [
                    ['label' => 'Каталог файлов', 'icon' => 'fa fa-minus', 'url' => ['/info/catalog/index']],
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
                'label' => 'Справочники',
                'icon' => 'fa fa-briefcase',
                'url' => '#',
                'items' => [
                    ['label' => 'Аудитории школы', 'icon' => 'fa fa-minus', 'url' => ['/auditory/default/index']],
                    ['label' => 'Места проведения', 'icon' => 'fa fa-minus', 'url' => ['/venue/default/index']],
                    ['label' => 'Дисциплины', 'icon' => 'fa fa-minus', 'url' => ['/subject/default/index']],
                    ['label' => 'Кадровая служба', 'icon' => 'fa fa-minus', 'url' => ['/guidejob/default/index']],
                    ['label' => 'Структура организации', 'icon' => 'fa fa-minus', 'url' => ['/own/default/index']],
                    ['label' => 'Учебные справочники', 'icon' => 'fa fa-minus', 'url' => ['/guidestudy/default/index']],
                    ['label' => 'Системные справочники', 'icon' => 'fa fa-minus', 'url' => ['/guidesys/default/index']],
                ],
            ],
            [
                'label' => 'Администрирование',
                'icon' => 'fa fa-cogs',
                'url' => '#',
                'items' => [
                    ['label' => 'Лог посещений', 'icon' => 'fa fa-minus', 'url' => ['/logs/default/index']],
                    ['label' => 'Пользователи', 'icon' => 'fa fa-minus', 'url' => ['/user/default/index'],],
                    ['label' => 'Очистить кэш', 'icon' => 'fa fa-minus', 'url' => ['/settings/cache/flush']],
                    ['label' => 'Настройки', 'icon' => 'fa fa-minus', 'url' => ['/settings/default/index']],
                ],
            ],
            [
                'label' => 'Разработка',
                'icon' => 'fa fa-file-code-o',
                'url' => '#',
                'items' => [
                    ['label' => 'Назначенные задания', 'icon' => 'fa fa-minus', 'url' => ['/queue-schedule/default/index']],
                    ['label' => 'Текстовые блоки', 'icon' => 'fa fa-minus', 'url' => ['/block/default/index']],
                    ['label' => 'Инструменты админа', 'icon' => 'fa fa-minus', 'url' => ['/admintools']],
                    ['label' => 'Debug', 'icon' => 'fa fa-minus', 'url' => ['/debug'], 'visible' => \artsoft\models\User::hasPermission('viewDebug')],
                    ['label' => 'Gii', 'icon' => 'fa fa-minus', 'url' => ['/gii'], 'visible' => \artsoft\models\User::hasPermission('viewGii')],
                ],
            ],
        ],
    ]) ?>
</div>
<!-- !SIDEBAR NAV -->