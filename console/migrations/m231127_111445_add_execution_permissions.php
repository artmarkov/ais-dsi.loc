<?php

use artsoft\db\PermissionsMigration;

class m231127_111445_add_execution_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('executionManagement', 'Управление разделом "Контроль исполнения"');
        $this->addRole('executionAdmin', 'Администратор раздела "Контроль исполнения"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('executionManagement');
        $this->deleteRole('executionAdmin');
    }

    public function getPermissions()
    {
        return [
            'executionManagement' => [
                'links' => [
                    '/execution/default/*',
                    '/execution/teachers/*',
                    '/admin/execution/default/*',
                ],
                'executionAccess' => [
                    'title' => 'Доступ к разделу "Контроль исполнения(frontend)"',
                    'links' => [
                        '/execution/default/thematic-sign',
                        '/execution/default/perform',
                        '/execution/default/progress',
                        '/execution/summary-progress/index',
                    ],
                    'roles' => [
                        self::ROLE_DEPARTMENT,
                    ],
                ],
                'teachersExecutionAccess' => [
                    'title' => 'Доступ к контролю преподавателей отдела(frontend)',
                    'links' => [
                        '/execution/teachers/index',
                        '/execution/teachers/view',
                        '/execution/teachers/load-items',
                        '/execution/teachers/load-items/grid-sort',
                        '/execution/teachers/load-items/grid-page-size',
                        '/execution/teachers/cheet-account',
                        '/execution/teachers/cheet-account/grid-sort',
                        '/execution/teachers/cheet-account/grid-page-size',
                        '/execution/teachers/schedule-items',
                        '/execution/teachers/schedule-items/grid-sort',
                        '/execution/teachers/schedule-items/grid-page-size',
                        '/execution/teachers/schedule',
                        '/execution/teachers/consult-items',
                        '/execution/teachers/consult-items/grid-sort',
                        '/execution/teachers/consult-items/grid-page-size',
                        '/execution/teachers/thematic-items',
                        '/execution/teachers/thematic-items?mode=view',
                        '/execution/teachers/thematic-items?mode=update',
                        '/execution/teachers/thematic-items/grid-sort',
                        '/execution/teachers/thematic-items/grid-page-size',
                        '/execution/teachers/studyplan-progress',
                        '/execution/teachers/studyplan-progress/grid-sort',
                        '/execution/teachers/studyplan-progress/grid-page-size',
                        '/execution/teachers/studyplan-progress-indiv',
                        '/execution/teachers/studyplan-progress-indiv/grid-sort',
                        '/execution/teachers/studyplan-progress-indiv/grid-page-size',
                        '/execution/teachers/efficiency',
                        '/execution/teachers/efficiency/grid-sort',
                        '/execution/teachers/efficiency/grid-page-size',
                        '/execution/teachers/portfolio',
                        '/execution/teachers/portfolio/grid-sort',
                        '/execution/teachers/portfolio/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_DEPARTMENT,
                    ],
                ],
                'fullExecutionAccess' => [
                    'title' => 'Администрирование раздела "Контроль исполнения"',
                    'links' => [
                        '/admin/execution/default/index',
                        '/admin/execution/default/consult',
                        '/admin/execution/default/perform',
                        '/admin/execution/default/thematic',
                        '/admin/execution/default/progress',
                        '/admin/execution/default/progress-confirm',
                        '/admin/execution/default/load',
                        '/admin/execution/default/load-studyplan',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                        'executionAdmin',
                    ],
                ],
            ],
        ];
    }

}
