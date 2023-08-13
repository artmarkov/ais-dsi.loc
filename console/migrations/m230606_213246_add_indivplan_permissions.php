<?php

use artsoft\db\PermissionsMigration;

class m230606_213246_add_indivplan_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('indivplanManagement', 'Управление Планированием Инд.занятий');
        $this->addRole('indivplanAdmin', 'Администратор Планирования Инд.занятий');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('indivplanManagement');
        $this->deleteRole('indivplanAdmin');
    }

    public function getPermissions()
    {
        return [
            'indivplanManagement' => [
                'links' => [
                    '/admin/indivplan/default/*',
                    '/admin/teachers/default/teachers-plan/*',
                    '/teachers/teachers-plan/*',
                ],
                'viewIndivplanBackend' => [
                    'title' => 'Просмотр планирования инд.занятий(backend)',
                    'links' => [
                        '/admin/indivplan/default/index',
                        '/admin/indivplan/default/grid-sort',
                        '/admin/indivplan/default/grid-page-size',
                        '/admin/teachers/default/teachers-plan',
                        '/admin/teachers/default/teachers-plan/grid-sort',
                        '/admin/teachers/default/teachers-plan/grid-page-size',
                        '/admin/teachers/default/teachers-plan?mode=view',

                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'viewIndivplanFrontend' => [
                    'title' => 'Просмотр планирования инд.занятий(frontend)',
                    'links' => [
                        '/teachers/teachers-plan/index',
                        '/teachers/teachers-plan/index/grid-sort',
                        '/teachers/teachers-plan/index/grid-page-size',

                    ],
                    'roles' => [
                        self::ROLE_TEACHER,
                    ],
                ],
                'fullIndivplanAccess' => [
                    'title' => 'Полный доступ к планированию инд.занятий',
                    'links' => [
                        '/admin/indivplan/default/create',
                        '/admin/indivplan/default/update',
                        '/admin/indivplan/default/delete',
                        '/admin/indivplan/default/history',
                        '/admin/indivplan/default/bulk-delete',
                        '/admin/teachers/default/teachers-plan?mode=create',
                        '/admin/teachers/default/teachers-plan?mode=update',
                        '/admin/teachers/default/teachers-plan?mode=delete',
                        '/admin/teachers/default/teachers-plan?mode=history',
                    ],
                    'roles' => [
                        'indivplanAdmin',
                    ],
                    'childs' => [
                        'viewIndivplanBackend',
                    ],
                ],
            ],
        ];
    }

}
