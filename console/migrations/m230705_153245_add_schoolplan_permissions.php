<?php

use artsoft\db\PermissionsMigration;

class m230705_153245_add_schoolplan_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('schoolplanManagement', 'Управление Планом работы');
        $this->addRole('schoolplanAdmin', 'Администратор Плана работы');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('schoolplanManagement');
        $this->deleteRole('schoolplanAdmin');
    }

    public function getPermissions()
    {
        return [
            'schoolplanManagement' => [
                'links' => [
                    '/schoolplan/default/*',
                    '/schoolplan/default/protocol-event/*',
                    '/schoolplan/default/protocol-attestations/*',
                    '/schoolplan/default/protocol-reception/*',
                    '/schoolplan/default/teachers-efficiency/*',
                    '/admin/schoolplan/default/*',
                    '/admin/schoolplan/default/protocol-event/*',
                    '/admin/schoolplan/default/protocol-attestations/*',
                    '/admin/schoolplan/default/protocol-reception/*',
                    '/admin/schoolplan/default/teachers-efficiency/*',
                ],
                'viewschoolplan' => [
                    'title' => 'Доступ к Плану работы(просмотр)',
                    'links' => [
                        '/schoolplan/default/index',
                        '/schoolplan/default/view',
                        '/schoolplan/default/grid-sort',
                        '/schoolplan/default/grid-page-size',
                        '/schoolplan/default/protocol-event',
                        '/schoolplan/default/protocol-event?mode=view',
                    ],
                    'roles' => [
                        self::ROLE_TEACHER,
                    ],
                ],
                'editschoolplan' => [
                    'title' => 'Доступ к Плану работы(редактирование)',
                    'links' => [
                        '/schoolplan/default/create',
                        '/schoolplan/default/update',
                        '/schoolplan/default/delete',
                        '/schoolplan/default/protocol-event?mode=create',
                        '/schoolplan/default/protocol-event?mode=update',
                        '/schoolplan/default/protocol-event?mode=delete',

                    ],
                    'roles' => [
                        self::ROLE_DEPARTMENT,
                    ],
                    'childs' => [
                        'viewschoolplan',
                    ],
                ],
                'fullschoolplanAccess' => [
                    'title' => 'Администрирование Плана работы',
                    'links' => [
                        '/admin/schoolplan/default/index',
                        '/admin/schoolplan/default/view',
                        '/admin/schoolplan/default/history',
                        '/admin/schoolplan/default/create',
                        '/admin/schoolplan/default/update',
                        '/admin/schoolplan/default/delete',
                        '/admin/schoolplan/default/grid-sort',
                        '/admin/schoolplan/default/grid-page-size',
                        '/admin/schoolplan/default/protocol-event',
                        '/admin/schoolplan/default/protocol-event?mode=create',
                        '/admin/schoolplan/default/protocol-event?mode=view',
                        '/admin/schoolplan/default/protocol-event?mode=update',
                        '/admin/schoolplan/default/protocol-event?mode=delete',
                        '/admin/schoolplan/default/protocol-event?mode=history',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                       'schoolplanAdmin',
                    ],
                ],
            ],
        ];
    }

}
