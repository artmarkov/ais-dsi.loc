<?php

use artsoft\db\PermissionsMigration;

class m230813_113246_add_teachers_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('teachersManagement', 'Управление Учебной Работой преподавателей');
        $this->addRole('teachersLoadAdmin', 'Администратор Нагрузки преподавателей');
        $this->addRole('teachersScheduleAdmin', 'Администратор Расписания занятий');
        $this->addRole('teachersConsultAdmin', 'Администратор Расписания консультаций');
        $this->addRole('teachersProgressAdmin', 'Администратор Журнала успеваемости');
        $this->addRole('teachersEfficiencyAdmin', 'Администратор Показателей эффективности');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('teachersManagement');
        $this->deleteRole('teachersLoadAdmin');
        $this->deleteRole('teachersScheduleAdmin');
        $this->deleteRole('teachersConsultAdmin');
        $this->deleteRole('teachersProgressAdmin');
        $this->deleteRole('teachersEfficiencyAdmin');
    }

    public function getPermissions()
    {
        return [
            'teachersManagement' => [
                'links' => [
                    '/admin/teachers/default/load-items/*',
                    '/admin/teachers/default/cheet-account/*',
                    '/admin/teachers/default/schedule-items/*',
                    '/admin/teachers/default/schedule/*',
                    '/admin/teachers/default/consult-itemsd/*',
                    '/admin/teachers/default/studyplan-progress/*',
                    '/admin/teachers/default/studyplan-progress-indiv/*',
                    '/admin/teachers/default/efficiency/*',
                    '/admin/teachers/default/portfolio/*',
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
                        '/teachers/load-items/index',
                        '/teachers/load-items/grid-sort',
                        '/teachers/load-items/grid-page-size',
                        '/teachers/cheet-account/index',
                        '/teachers/cheet-account/grid-sort',
                        '/teachers/cheet-account/grid-page-size',
                        '/teachers/schedule-items/index',
                        '/teachers/schedule-items/grid-sort',
                        '/teachers/schedule-items/grid-page-size',
                        '/teachers/schedule/index',
                        '/teachers/consult-items/index',
                        '/teachers/consult-items/grid-sort',
                        '/teachers/consult-items/grid-page-size',
                        '/teachers/consult-items/create',
                        '/teachers/consult-items/update',
                        '/teachers/consult-items/delete',
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
                    'title' => 'Доступ к личному кабинету преподавателя(backend)',
                    'links' => [
                        '/admin/teachers/default/load-items',
                        '/admin/teachers/default/load-items/grid-sort',
                        '/admin/teachers/default/load-items/grid-page-size',
                        '/admin/teachers/default/cheet-account',
                        '/admin/teachers/default/cheet-account/grid-sort',
                        '/admin/teachers/default/cheet-account/grid-page-size',
                        '/admin/teachers/default/schedule-items',
                        '/admin/teachers/default/schedule-items/grid-sort',
                        '/admin/teachers/default/schedule-items/grid-page-size',
                        '/admin/teachers/default/schedule',
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

                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'fullTeachersLoadAccess' => [
                    'title' => 'Полный доступ к нагрузке преподавателя',
                    'links' => [
                        '/admin/teachers/default/load-items?mode=create',
                        '/admin/teachers/default/load-items?mode=update',
                        '/admin/teachers/default/load-items?mode=delete',
                        '/admin/teachers/default/load-items?mode=history',
                    ],
                    'roles' => [
                        'teachersLoadAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersScheduleAccess' => [
                    'title' => 'Полный доступ к расписанию преподавателя',
                    'links' => [
                        '/admin/teachers/default/schedule-items?mode=create',
                        '/admin/teachers/default/schedule-items?mode=update',
                        '/admin/teachers/default/schedule-items?mode=delete',
                        '/admin/teachers/default/schedule-items?mode=history',
                    ],
                    'roles' => [
                        'teachersScheduleAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersConsultAccess' => [
                    'title' => 'Полный доступ к расписанию консультаций преподавателя',
                    'links' => [
                        '/admin/teachers/default/consult-items?mode=create',
                        '/admin/teachers/default/consult-items?mode=update',
                        '/admin/teachers/default/consult-items?mode=delete',
                        '/admin/teachers/default/consult-items?mode=history',
                    ],
                    'roles' => [
                        'teachersConsultAdmin',
                    ],
                    'childs' => [
                        'accessTeachersBackend',
                    ],
                ],
                'fullTeachersProgressAccess' => [
                    'title' => 'Полный доступ к журналу успеваемости',
                    'links' => [
                        '/admin/teachers/default/studyplan-progress?mode=create',
                        '/admin/teachers/default/studyplan-progress?mode=update',
                        '/admin/teachers/default/studyplan-progress?mode=delete',
                        '/admin/teachers/default/studyplan-progress?mode=history',
                        '/admin/teachers/default/studyplan-progress-indiv?mode=create',
                        '/admin/teachers/default/studyplan-progress-indiv?mode=update',
                        '/admin/teachers/default/studyplan-progress-indiv?mode=delete',
                        '/admin/teachers/default/studyplan-progress-indiv?mode=history',
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

            ],
        ];
    }

}
