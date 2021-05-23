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
        'items'  => [
            [
                'label' => 'Главная',
                'icon' => 'fa fa-th',
                'url' => '/admin',
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
                    ['label' => 'Заявления', 'icon' => 'fa fa-minus', 'url' => ['/order/index']],
                ],
            ],
            [
                'label' => 'Организационная работа',
                'icon' => 'fa fa-university',
                'url' => '#',
                'items' => [
                    ['label' => 'Учебные планы', 'icon' => 'fa fa-minus', 'url' => ['/studyplan/default/index']],
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
                    ['label' => 'Вступительные экзамены', 'icon' => 'fa fa-minus', 'url' => ['/examination/default/index']],
                    ['label' => 'Движение учеников', 'icon' => 'fa fa-minus', 'url' => ['/attandlog/index']],
                    ['label' => 'Расписание занятий', 'icon' => 'fa fa-minus', 'url' => ['/attandlog/index']],
                    ['label' => 'Календарь мероприятий', 'icon' => 'fa fa-minus', 'url' => ['/activities/default/index']],
                    ['label' => 'Журнал успеваемости', 'icon' => 'fa fa-minus', 'url' => ['/attandlog/index']],
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
                    ['label' => 'Портфолио преподавателей', 'icon' => 'fa fa-minus', 'url' => ['/visual/index']],
                    ['label' => 'Контроль исполнения', 'icon' => 'fa fa-minus', 'url' => ['/visual/index']],
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
                'label' => 'Помощь',
                'icon' => 'fa fa-question-circle',
                'url' => '#',
                'items' => [
                    ['label' => 'Техническая поддержка', 'icon' => 'fa fa-minus', 'url' => ['/site/support']],
                    ['label' => 'Руководства пользователя', 'icon' => 'fa fa-minus', 'url' => ['/site/help']],
                    ['label' => 'О системе', 'icon' => 'fa fa-minus', 'url' => ['/site/about']],
                ],
            ],
            [
                'label' => 'Разработка',
                'icon' => 'fa fa-file-code-o',
                'url' => '#',
                'items' => [
                    ['label' => 'Назначенные задания', 'icon' => 'fa fa-minus', 'url' => ['/queue-schedule/default/index']],
                    ['label' => 'Инструменты админа', 'icon' => 'fa fa-minus', 'url' => ['/admintools']],
                    ['label' => 'Debug', 'icon' => 'fa fa-minus', 'url' => ['/debug'], 'visible' => isset(Yii::$app->modules['debug'])],
                    ['label' => 'Gii', 'icon' => 'fa fa-minus', 'url' => ['/gii'], 'visible' => Yii::$app->getModule('gii') !== null],
                ],
            ],
        ],
    ]) ?>
<!--    --><?php //print_r(Menu::getMenuItems('admin-menu'))?>
</div>
<!-- !SIDEBAR NAV -->