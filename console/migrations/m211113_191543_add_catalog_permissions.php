<?php

use artsoft\db\PermissionsMigration;

class m211113_191543_add_catalog_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('catalogManagement', 'Управление каталогом файлов');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('catalogManagement');
    }

    public function getPermissions()
    {
        return [
            'catalogManagement' => [
                'links' => [
                    '/info/catalog/*',
                    '/admin/info/catalog/*',
                ],
                'viewCatalog' => [
                    'title' => 'Доступ к frontend-каталогу',
                    'links' => [
                        '/info/catalog/index',
                        '/info/catalog/edit',
                        '/info/catalog/check',
                    ],
                    'roles' => [
                        self::ROLE_USER,
                        self::ROLE_STUDENT,
                        self::ROLE_PARENTS,
                    ],
                ],
                'editCatalog' => [
                    'title' => 'Редактирование каталога',
                    'links' => [
                        '/admin/info/catalog/index',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewCatalog',
                    ],
                ],
                'allowNewRootsCatalog' => [
                    'title' => 'Создавать корневую директорию',
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewCatalog',
                    ],
                ],
            ],
        ];
    }

}
