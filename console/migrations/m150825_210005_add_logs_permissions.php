<?php

use artsoft\db\PermissionsMigration;

class m150825_210005_add_logs_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('logsManagement', 'Управление логами');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('logsManagement');
    }

    public function getPermissions()
    {
        return [
            'logsManagement' => [
                'links' => [
                    '/admin/logs/*',
                    '/admin/logs/default/*',
                ],

                'viewVisitLog' => [
                    'title' => 'Просмотр Журнала посещений',
                    'roles' => [self::ROLE_SYSTEM],
                    'links' => [
                        '/admin/logs/default/index',
                        '/admin/logs/default/view',
                        '/admin/logs/default/grid-sort',
                        '/admin/logs/default/grid-page-size',
                    ],
                ],
                'viewSession' => [
                    'title' => 'Просмотр Сессий',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewVisitLog'],
                    'links' => [
                        '/admin/logs/session/index',
                        '/admin/logs/session/grid-sort',
                        '/admin/logs/session/grid-page-size',
                    ],
                ],
                'viewRequest' => [
                    'title' => 'Просмотр Запросов',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewVisitLog'],
                    'links' => [
                        '/admin/logs/request/index',
                        '/admin/logs/request/grid-sort',
                        '/admin/logs/request/grid-page-size',
                    ],
                ],
            ],
        ];
    }

}
