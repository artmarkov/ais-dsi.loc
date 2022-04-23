<?php

use artsoft\db\PermissionsMigration;

class m190611_121414_add_dbmanager_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('dbManagement', 'Менеджер БД');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('dbManagement');
    }

    public function getPermissions()
    {
        return [
            'dbManagement' => [
                'links' => [
                    '/admin/dbmanager/*',
                    '/admin/dbmanager/default/*',
                ],
                'viewDb' => [
                    'title' => 'Просмотр БД',
                    'links' => [
                        '/admin/dbmanager/default/index',
                        '/admin/dbmanager/default/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'downloadDb' => [
                    'title' => 'Загрузка дампа БД',
                    'links' => [
                        '/admin/dbmanager/default/download',  
                    ],
                    'roles' => [
                        self::ROLE_DEV,
                    ],
                    'childs' => [
                        'viewDb',
                    ],
                ],
                'exportDb' => [
                    'title' => 'Экспорт дампа БД',
                    'links' => [
                        '/admin/dbmanager/default/export',
                    ],
                    'roles' => [
                        self::ROLE_DEV,
                    ],
                    'childs' => [
                        'viewDb',
                    ],
                ],
                'importDb' => [
                    'title' => 'Импорт дампа БД',
                    'links' => [
                        '/admin/dbmanager/default/import',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                    'childs' => [
                        'viewDb',
                    ],
                ],
                'deleteDb' => [
                    'title' => 'Удаление дампа БД',
                    'links' => [
                        '/admin/dbmanager/default/delete',
                        '/admin/dbmanager/default/delete-all',
                    ],
                    'roles' => [
                        self::ROLE_DEV,
                    ],
                    'childs' => [
                        'viewDb',
                    ],
                ],                          
            ],
        ];
    }

}
