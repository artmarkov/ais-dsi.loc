<?php

use artsoft\db\PermissionsMigration;

class m230813_113246_add_teachers_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('teachersManagement', 'Управление Учебной Работой преподавателей');
        $this->addRole('teachersAdmin', 'Администратор Карточки преподавателей');
        $this->addRole('teachersLoadAdmin', 'Администратор Нагрузки преподавателей');
        $this->addRole('teachersScheduleAdmin', 'Администратор Расписания занятий');
        $this->addRole('teachersConsultAdmin', 'Администратор Расписания консультаций');
        $this->addRole('teachersProgressAdmin', 'Администратор Журнала успеваемости');
        $this->addRole('teachersEfficiencyAdmin', 'Администратор Показателей эффективности');
        $this->addRole('teachersСharacteristicAdmin', 'Администратор Характеристик по предмету');
        $this->addRole('teachersThematicAdmin', 'Администратор Тематических планов');
        $this->addRole('teachersInvoicesAdmin', 'Администратор Оплаты за обучение');
        $this->addRole('teachersPerformAdmin', 'Администратор Выполнения плана и участия в мероприятиях');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('teachersManagement');
        $this->deleteRole('teachersAdmin');
        $this->deleteRole('teachersLoadAdmin');
        $this->deleteRole('teachersScheduleAdmin');
        $this->deleteRole('teachersConsultAdmin');
        $this->deleteRole('teachersProgressAdmin');
        $this->deleteRole('teachersEfficiencyAdmin');
        $this->deleteRole('teachersСharacteristicAdmin');
        $this->deleteRole('teachersThematicAdmin');
        $this->deleteRole('teachersInvoicesAdmin');
        $this->deleteRole('teachersPerformAdmin');
    }

    public function getPermissions()
    {
        return [
            'teachersManagement' => [
                'links' => [
                    '/admin/invoices/default/*',
                    '/admin/teachers/default/*',
                    '/admin/teachers/default/load-items/*',
                    '/admin/teachers/default/cheet-account/*',
                    '/admin/teachers/default/schedule-items/*',
                    '/admin/teachers/default/schedule/*',
                    '/admin/teachers/default/consult-items/*',
                    '/admin/teachers/default/studyplan-progress/*',
                    '/admin/teachers/default/studyplan-progress-indiv/*',
                    '/admin/teachers/default/efficiency/*',
                    '/admin/teachers/default/portfolio/*',
                    '/teachers/default/*',
                    '/teachers/load-items/*',
                    '/teachers/cheet-account/*',
                    '/teachers/schedule-items/*',
                    '/teachers/schedule/*',
                    '/teachers/consult-items/*',
                    '/teachers/studyplan-progress/*',
                    '/teachers/studyplan-progress-indiv/*',
                    '/teachers/efficiency/*',
                    '/teachers/portfolio/*',
                ],
                'accessTeachersFrontend' => [
                    'title' => 'Доступ к личному кабинету преподавателя(frontend)',
                    'links' => [
                        '/teachers/default/index',
                        '/teachers/students/grid-sort',
                        '/teachers/students/grid-page-size',
                        '/teachers/students/index',
                        '/teachers/students/view',
                        '/teachers/load-items/index',
                        '/teachers/load-items/grid-sort',
                        '/teachers/load-items/grid-page-size',
                        '/teachers/cheet-account/index',
                        '/teachers/cheet-account/grid-sort',
                        '/teachers/cheet-account/grid-page-size',
                        '/teachers/schedule-items/index',
                        '/teachers/schedule-items/create',
                        '/teachers/schedule-items/update',
                        '/teachers/schedule-items/delete',
                        '/teachers/schedule-items/grid-sort',
                        '/teachers/schedule-items/grid-page-size',
                        '/teachers/schedule-items/schedule',
                        '/teachers/consult-items/index',
                        '/teachers/consult-items/create',
                        '/teachers/consult-items/update',
                        '/teachers/consult-items/delete',
                        '/teachers/consult-items/grid-sort',
                        '/teachers/consult-items/grid-page-size',
                        '/teachers/studyplan-progress/index',
                        '/teachers/studyplan-progress/grid-sort',
                        '/teachers/studyplan-progress/grid-page-size',
                        '/teachers/studyplan-progress/create',
                        '/teachers/studyplan-progress/update',
                        '/teachers/studyplan-progress/delete',
                        '/teachers/studyplan-progress-indiv/index',
                        '/teachers/studyplan-progress-indiv/grid-sort',
                        '/teachers/studyplan-progress-indiv/grid-page-size',
                        '/teachers/studyplan-progress-indiv/create',
                        '/teachers/studyplan-progress-indiv/update',
                        '/teachers/studyplan-progress-indiv/delete',
                        '/teachers/efficiency/index',
                        '/teachers/efficiency/bar',
                        '/teachers/efficiency/grid-sort',
                        '/teachers/efficiency/grid-page-size',
                        '/teachers/portfolio/index',
                        '/teachers/portfolio/grid-sort',
                        '/teachers/portfolio/grid-page-size',

                    ],
                    'roles' => [
                        self::ROLE_TEACHER,
                    ],
                ],
                'accessTeachersBackend' => [
                    'title' => 'Доступ к учебной работе(backend)',
                    'links' => [
                        '/admin/invoices/default/index',
                        '/admin/teachers/default/index',
                        '/admin/teachers/default/load-items',
                        '/admin/teachers/default/load-items/grid-sort',
                        '/admin/teachers/default/load-items/grid-page-size',
                        '/admin/teachers/default/cheet-account',
                        '/admin/teachers/default/cheet-account/grid-sort',
                        '/admin/teachers/default/cheet-account/grid-page-size',
                        '/admin/teachers/default/schedule-items',
                        '/admin/teachers/default/schedule-items/grid-sort',
                        '/admin/teachers/default/schedule-items/grid-page-size',
                        '/admin/teachers/default/schedule-items/schedule',
                        '/admin/teachers/default/consult-items',
                        '/admin/teachers/default/consult-items/grid-sort',
                        '/admin/teachers/default/consult-items/grid-page-size',
                        '/admin/teachers/default/studyplan-progress',
                        '/admin/teachers/default/studyplan-progress/grid-sort',
                        '/admin/teachers/default/studyplan-progress/grid-page-size',
                        '/admin/teachers/default/studyplan-progress-indiv',
                        '/admin/teachers/default/studyplan-progress-indiv/grid-sort',
                        '/admin/teachers/default/studyplan-progress-indiv/grid-page-size',
                        '/admin/teachers/default/efficiency',
                        '/admin/teachers/default/efficiency/grid-sort',
                        '/admin/teachers/default/efficiency/grid-page-size',
                        '/admin/teachers/default/portfolio',
                        '/admin/teachers/default/portfolio/grid-sort',
                        '/admin/teachers/default/portfolio/grid-page-size',
                        '/admin/studyplan/default/index',
                        '/admin/studyplan/default/students-view',
                        '/admin/studyplan/default/load-items',
                        '/admin/studyplan/default/load-items/grid-sort',
                        '/admin/studyplan/default/load-items/grid-page-size',
                        '/admin/studyplan/default/schedule-items',
                        '/admin/studyplan/default/schedule-items/grid-sort',
                        '/admin/studyplan/default/schedule-items/grid-page-size',
                        '/admin/studyplan/default/schedule-items/schedule',
                        '/admin/studyplan/default/consult-items',
                        '/admin/studyplan/default/consult-items/grid-sort',
                        '/admin/studyplan/default/consult-items/grid-page-size',
                        '/admin/studyplan/default/characteristic-items',
                        '/admin/studyplan/default/characteristic-items/grid-sort',
                        '/admin/studyplan/default/characteristic-items/grid-page-size',
                        '/admin/studyplan/default/thematic-items',
                        '/admin/studyplan/default/thematic-items/grid-sort',
                        '/admin/studyplan/default/thematic-items/grid-page-size',
                        '/admin/studyplan/default/studyplan-progress',
                        '/admin/studyplan/default/studyplan-progress/grid-sort',
                        '/admin/studyplan/default/studyplan-progress/grid-page-size',
                        '/admin/studyplan/default/studyplan-invoices',
                        '/admin/studyplan/default/studyplan-invoices/grid-sort',
                        '/admin/studyplan/default/studyplan-invoices/grid-page-size',
                        '/admin/studyplan/default/studyplan-perform',
                        '/admin/studyplan/default/studyplan-perform/grid-sort',
                        '/admin/studyplan/default/studyplan-perform/grid-page-size',
                        '/admin/sect/default/index',
                        '/admin/sect/default/distribution',
                        '/admin/sect/default/distribution/grid-sort',
                        '/admin/sect/default/distribution/grid-page-size',
                        '/admin/sect/default/load-items',
                        '/admin/sect/default/load-items/grid-sort',
                        '/admin/sect/default/load-items/grid-page-size',
                        '/admin/sect/default/schedule-items',
                        '/admin/sect/default/schedule-items/grid-sort',
                        '/admin/sect/default/schedule-items/grid-page-size',
                        '/admin/sect/default/schedule-items/schedule',
                        '/admin/sect/default/consult-items',
                        '/admin/sect/default/consult-items/grid-sort',
                        '/admin/sect/default/consult-items/grid-page-size',
                        '/admin/sect/default/studyplan-progress',
                        '/admin/sect/default/studyplan-progress/grid-sort',
                        '/admin/sect/default/studyplan-progress/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'fullTeachersAccess' => [
                    'title' => 'Полный доступ к карточке преподавателя',
                    'links' => [
                        '/admin/teachers/default/create',
                        '/admin/teachers/default/update',
                        '/admin/teachers/default/delete',
                        '/admin/teachers/default/history',
                    ],
                    'roles' => [
                        'teachersAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersLoadAccess' => [
                    'title' => 'Полный доступ к нагрузке',
                    'links' => [
                        '/admin/teachers/default/load-items?mode=create',
                        '/admin/teachers/default/load-items?mode=update',
                        '/admin/teachers/default/load-items?mode=delete',
                        '/admin/teachers/default/load-items?mode=history',
                        '/admin/studyplan/default/load-items?mode=create',
                        '/admin/studyplan/default/load-items?mode=update',
                        '/admin/studyplan/default/load-items?mode=delete',
                        '/admin/studyplan/default/load-items?mode=history',
                        '/admin/sect/default/load-items?mode=create',
                        '/admin/sect/default/load-items?mode=update',
                        '/admin/sect/default/load-items?mode=delete',
                        '/admin/sect/default/load-items?mode=history',
                    ],
                    'roles' => [
                        'teachersLoadAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersScheduleAccess' => [
                    'title' => 'Полный доступ к расписанию',
                    'links' => [
                        '/admin/teachers/default/schedule-items?mode=create',
                        '/admin/teachers/default/schedule-items?mode=update',
                        '/admin/teachers/default/schedule-items?mode=delete',
                        '/admin/teachers/default/schedule-items?mode=history',
                        '/admin/studyplan/default/schedule-items?mode=create',
                        '/admin/studyplan/default/schedule-items?mode=update',
                        '/admin/studyplan/default/schedule-items?mode=delete',
                        '/admin/studyplan/default/schedule-items?mode=history',
                        '/admin/sect/default/schedule-items?mode=create',
                        '/admin/sect/default/schedule-items?mode=update',
                        '/admin/sect/default/schedule-items?mode=delete',
                        '/admin/sect/default/schedule-items?mode=history',
                    ],
                    'roles' => [
                        'teachersScheduleAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersConsultAccess' => [
                    'title' => 'Полный доступ к расписанию консультаций',
                    'links' => [
                        '/admin/teachers/default/consult-items?mode=create',
                        '/admin/teachers/default/consult-items?mode=update',
                        '/admin/teachers/default/consult-items?mode=delete',
                        '/admin/teachers/default/consult-items?mode=history',
                        '/admin/studyplan/default/consult-items?mode=create',
                        '/admin/studyplan/default/consult-items?mode=update',
                        '/admin/studyplan/default/consult-items?mode=delete',
                        '/admin/studyplan/default/consult-items?mode=history',
                        '/admin/sect/default/consult-items?mode=create',
                        '/admin/sect/default/consult-items?mode=update',
                        '/admin/sect/default/consult-items?mode=delete',
                        '/admin/sect/default/consult-items?mode=history',
                    ],
                    'roles' => [
                        'teachersConsultAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersProgressAccess' => [
                    'title' => 'Полный доступ к журналу и дневнику успеваемости',
                    'links' => [
                        '/admin/teachers/default/studyplan-progress?mode=create',
                        '/admin/teachers/default/studyplan-progress?mode=update',
                        '/admin/teachers/default/studyplan-progress?mode=delete',
                        '/admin/teachers/default/studyplan-progress?mode=history',
                        '/admin/teachers/default/studyplan-progress-indiv?mode=create',
                        '/admin/teachers/default/studyplan-progress-indiv?mode=update',
                        '/admin/teachers/default/studyplan-progress-indiv?mode=delete',
                        '/admin/teachers/default/studyplan-progress-indiv?mode=history',
                        '/admin/studyplan/default/studyplan-progress?mode=create',
                        '/admin/studyplan/default/studyplan-progress?mode=update',
                        '/admin/studyplan/default/studyplan-progress?mode=delete',
                        '/admin/studyplan/default/studyplan-progress?mode=history',
                        '/admin/sect/default/studyplan-progress?mode=create',
                        '/admin/sect/default/studyplan-progress?mode=update',
                        '/admin/sect/default/studyplan-progress?mode=delete',
                        '/admin/sect/default/studyplan-progress?mode=history',
                    ],
                    'roles' => [
                        'teachersProgressAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersEfficiencyAccess' => [
                    'title' => 'Полный доступ к показателям эффективности преподавателя',
                    'links' => [
                        '/admin/teachers/default/efficiency?mode=create',
                        '/admin/teachers/default/efficiency?mode=update',
                        '/admin/teachers/default/efficiency?mode=delete',
                        '/admin/teachers/default/efficiency?mode=history',
                    ],
                    'roles' => [
                        'teachersEfficiencyAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersCharacteristicAccess' => [
                    'title' => 'Полный доступ к характеристикам по предметам',
                    'links' => [
                        '/admin/studyplan/default/characteristic-items?mode=create',
                        '/admin/studyplan/default/characteristic-items?mode=update',
                        '/admin/studyplan/default/characteristic-items?mode=delete',
                        '/admin/studyplan/default/characteristic-items?mode=history',
                    ],
                    'roles' => [
                        'teachersСharacteristicAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersThematicAccess' => [
                    'title' => 'Полный доступ к тематическим планам',
                    'links' => [
                        '/admin/studyplan/default/thematic-items?mode=create',
                        '/admin/studyplan/default/thematic-items?mode=update',
                        '/admin/studyplan/default/thematic-items?mode=delete',
                        '/admin/studyplan/default/thematic-items?mode=history',
                    ],
                    'roles' => [
                        'teachersThematicAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersInvoicesAccess' => [
                    'title' => 'Полный доступ к Оплате за обучение',
                    'links' => [
                        '/admin/studyplan/default/studyplan-invoices?mode=create',
                        '/admin/studyplan/default/studyplan-invoices?mode=update',
                        '/admin/studyplan/default/studyplan-invoices?mode=delete',
                        '/admin/studyplan/default/studyplan-invoices?mode=history',
                        '/admin/invoices/default/create',
                        '/admin/invoices/default/update',
                        '/admin/invoices/default/delete',
                        '/admin/invoices/default/history',
                    ],
                    'roles' => [
                        'teachersInvoicesAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersPerformAccess' => [
                    'title' => 'Полный доступ к Выполнению и участию в мероприятий',
                    'links' => [
                        '/admin/studyplan/default/studyplan-perform?mode=create',
                        '/admin/studyplan/default/studyplan-perform?mode=update',
                        '/admin/studyplan/default/studyplan-perform?mode=delete',
                        '/admin/studyplan/default/studyplan-perform?mode=history',
                    ],
                    'roles' => [
                        'teachersPerformAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
            ],
        ];
    }

}