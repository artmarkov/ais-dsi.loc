<?php

use artsoft\models\User;
use artsoft\widgets\Nav;

?>
<!-- SIDEBAR NAV -->
<div class="navbar-default sidebar metismenu" role="navigation">
    <?php
    $pre_status = Yii::$app->settings->get('module.pre_status');
    $pre_date_in = Yii::$app->formatter->asTimestamp(Yii::$app->settings->get('module.pre_date_in'));
    $pre_date_out = Yii::$app->formatter->asTimestamp(Yii::$app->settings->get('module.pre_date_out'));
//    print_r([$pre_status,$pre_date_in,$pre_date_out,time()]);
    ?>
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
                'label' => 'Запись на обучение',
                'icon' => 'fa fa-th',
                'url' => ['/preregistration/default/finding'],
                'visible' => (Yii::$app->user->isGuest && $pre_status == 1 && $pre_date_in < time() && $pre_date_out > time())
            ],
            [
                'label' => 'Формы и заявки',
                'icon' => 'fa fa-th',
                'url' => ['/question/default/index'],
                'visible' => Yii::$app->user->isGuest
            ],
            [
                'label' => 'Информационный ресурс',
                'icon' => 'fa fa-inbox',
                'url' => '#',
                'items' => [
                    ['label' => 'Каталог файлов', 'icon' => 'fa fa-minus', 'url' => ['/info/catalog/index']],
                    ['label' => 'Объявления', 'icon' => 'fa fa-minus', 'url' => ['/info/board/index']],
                    ['label' => 'Почта', 'icon' => 'fa fa-minus', 'url' => ['/mailbox/default/index']],
                    ['label' => 'Формы и заявки', 'icon' => 'fa fa-minus', 'url' => ['/question/student/index'], 'visible' => User::hasRole(['student'], false)],
                    ['label' => 'Формы и заявки', 'icon' => 'fa fa-minus', 'url' => ['/question/parent/index'], 'visible' => User::hasRole(['parents'], false)],
                    ['label' => 'Формы и заявки', 'icon' => 'fa fa-minus', 'url' => ['/question/teachers/index'], 'visible' => User::hasRole(['teacher','department'], false)],
                    ['label' => 'Конкурсы', 'icon' => 'fa fa-minus', 'url' => ['/concourse/default/index'], 'visible' => User::hasRole(['teacher','department'], false)],
                ],
            ],

            [
                'label' => 'Реестры',
                'icon' => 'fa fa-list',
                'url' => '#',
                'items' => [
                    ['label' => 'Список учеников', 'icon' => 'fa fa-minus', 'url' => ['/reestr/studyplan/index']],
                    ['label' => 'Список сотрудников', 'icon' => 'fa fa-minus', 'url' => ['/reestr/employees/index']],
                    ['label' => 'Список преподавателей', 'icon' => 'fa fa-minus', 'url' => ['/reestr/teachers/index']],
                    ['label' => 'Список родителей', 'icon' => 'fa fa-minus', 'url' => ['/reestr/parents/index']],
                ],
            ],
            [
                'label' => 'Организационная работа',
                'icon' => 'fa fa-university',
                'url' => '#',
                'items' => [
                    ['label' => 'Производственный календарь', 'icon' => 'fa fa-minus', 'url' => ['/routine/default/calendar']],
                    ['label' => 'Сетка расписания школы', 'icon' => 'fa fa-minus', 'url' => ['/schedule/default/index']], // common permission
                    ['label' => 'Календарь мероприятий', 'icon' => 'fa fa-minus', 'url' => ['/activities/default/calendar']],
                    ['label' => 'Календарь мероприятий', 'icon' => 'fa fa-minus', 'url' => ['/activities/student-schedule/index'], 'visible' => User::hasRole(['student','parents'])],
                    ['label' => 'План работы школы', 'icon' => 'fa fa-minus', 'url' => ['/schoolplan/default/index']],
                    ['label' => 'Счета за обучение', 'icon' => 'fa fa-minus', 'url' => ['/invoices/default/index']],
//                    ['label' => 'Табель учета пед.часов', 'icon' => 'fa fa-minus', 'url' => ['/timesheet/default/index']],
//                    ['label' => 'Работы и сертификаты', 'icon' => 'fa fa-minus', 'url' => ['/creative/default/index']],
                ],
            ],
            [
                'label' => 'Учебная работа',
                'icon' => 'fa fa-graduation-cap',
                'url' => '#',
                'items' => [
                    // Преподаватели
                    ['label' => 'Карточка преподавателя', 'icon' => 'fa fa-minus', 'url' => ['/teachers/default/index']],
                    ['label' => 'Ученики и группы', 'icon' => 'fa fa-minus', 'url' => ['/teachers/studyplan/index']],
                    ['label' => 'Нагрузка', 'icon' => 'fa fa-minus', 'url' => ['/teachers/load-items/index']],
                    ['label' => 'Табель учета', 'icon' => 'fa fa-minus', 'url' => ['/teachers/cheet-account/index']],
                    ['label' => 'Планирование индивидуальных занятий', 'icon' => 'fa fa-minus', 'url' => ['/teachers/teachers-plan/index']],
                    ['label' => 'Расписание занятий', 'icon' => 'fa fa-minus', 'url' => ['/teachers/schedule-items/index']],
                    ['label' => 'Расписание консультаций', 'icon' => 'fa fa-minus', 'url' => ['/teachers/consult-items/index']],
                    ['label' => 'Тематические/репертуарные планы', 'icon' => 'fa fa-minus', 'url' => ['/teachers/thematic-items/index']],
                    ['label' => 'Журнал успеваемости группы', 'icon' => 'fa fa-minus', 'url' => ['/teachers/studyplan-progress/index']],
                    ['label' => 'Журнал успеваемости индивидуальных занятий', 'icon' => 'fa fa-minus', 'url' => ['/teachers/studyplan-progress-indiv/index']],
                    ['label' => 'Показатели эффективности', 'icon' => 'fa fa-minus', 'url' => ['/teachers/efficiency/index']],
                    ['label' => 'Портфолио', 'icon' => 'fa fa-minus', 'url' => ['/teachers/portfolio/index']],
                    ['label' => 'Работы и сертификаты', 'icon' => 'fa fa-minus', 'url' => ['/teachers/creative/index']],
                    ['label' => 'Документы', 'icon' => 'fa fa-minus', 'url' => ['/teachers/document/index']],
                    ['label' => 'Вступительные экзамены', 'icon' => 'fa fa-minus', 'url' => ['/entrant/default/index']],
                    // ученики
                    ['label' => 'Карточка ученика', 'icon' => 'fa fa-minus', 'url' => ['/student/default/index']],
                    ['label' => 'Планы учащегося', 'icon' => 'fa fa-minus', 'url' => ['/studyplan/default/index']],
                    // родители
                    ['label' => 'Карточка родителя', 'icon' => 'fa fa-minus', 'url' => ['/parents/default/index']],
                    ['label' => 'Планы учащихся', 'icon' => 'fa fa-minus', 'url' => ['/parents/studyplan/index']],

                ],
            ],
            [
                'label' => 'Контроль исполнения',
                'icon' => 'fa fa-bar-chart',
                'url' => '#',
                'items' => [
                    ['label' => 'Тематические/репертуарные планы на подписи', 'icon' => 'fa fa-minus', 'url' => ['/execution/default/thematic-sign']],
                    ['label' => 'Контроль выполнения планов отдела', 'icon' => 'fa fa-minus', 'url' => ['/execution/default/perform']],
                    ['label' => 'Контроль заполнения журналов успеваемости', 'icon' => 'fa fa-minus', 'url' => ['/execution/default/progress']],
                    ['label' => 'Отдел на контроле', 'icon' => 'fa fa-minus', 'url' => ['/execution/teachers/index']],
//                    ['label' => 'Сводная успеваемость', 'icon' => 'fa fa-minus', 'url' => ['/summary-progress/default/index']],
                ],
            ],
            [
                'label' => 'Сервис',
                'icon' => 'fa fa-industry',
                'url' => '#',
                'items' => [
                    ['label' => 'Журнал пропусков', 'icon' => 'fa fa-minus', 'url' => ['/service/default/index']],
                    ['label' => 'Журнал выдачи ключей', 'icon' => 'fa fa-minus', 'url' => ['/service/attendlog/index']],
//                    ['label' => 'Журнал СКУД', 'icon' => 'fa fa-minus', 'url' => ['/service/sigur/index']],
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