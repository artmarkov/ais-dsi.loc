<?php

use artsoft\db\PermissionsMigration;

class m210301_150400_add_own_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('ownManagement', 'Справочник "Структура организации"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('ownManagement');
    }

    public function getPermissions()
    {
        return [
            'ownManagement' => [
                'links' => [
                    '/admin/own/*',
                    '/admin/own/default/*',
                    '/admin/own/division/*',
                    '/admin/own/department/*',
                ],
                'viewOwn' => [
                    'title' => 'Просмотр "Структура организации"',
                    'links' => [
                        '/admin/own/default/index',
                        '/admin/own/default/view',
                        '/admin/own/default/grid-page-size',
                        '/admin/own/division/index',
                        '/admin/own/division/view',
                        '/admin/own/division/grid-page-size',
                        '/admin/own/department/index',
                        '/admin/own/department/view',
                        '/admin/own/department/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                ],
                'editOwn' => [
                    'title' => 'Редактирование записи "Структура организации"',
                    'links' => [
                        '/admin/own/default/update',
                        '/admin/own/division/update',
                        '/admin/own/department/update',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewOwn',
                    ],
                ],
                'createOwn' => [
                    'title' => 'Добавление записи в "Структура организации"',
                    'links' => [
                        '/admin/own/default/create',
                        '/admin/own/division/create',
                        '/admin/own/department/create',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewOwn',
                    ],
                ],
                'deleteOwn' => [
                    'title' => 'Удаление записи из "Структура организации"',
                    'links' => [
                        '/admin/own/default/delete',
                        '/admin/own/default/bulk-delete',
                        '/admin/own/division/delete',
                        '/admin/own/division/bulk-delete',
                        '/admin/own/department/delete',
                        '/admin/own/department/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'createOwn',
                    ],
                ],
            ],
        ];
    }

}
