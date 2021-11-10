<?php

use artsoft\db\PermissionsMigration;

class m160418_220620_add_block_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('blockManagement', 'Текстовые блоки');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('blockManagement');
    }

    public function getPermissions()
    {
        return [
            'blockManagement' => [
                'links' => [
                    '/admin/block/*',
                    '/admin/block/default/*',
                ],
                'viewBlocks' => [
                    'title' => 'Просмотр блоков',
                    'roles' => [self::ROLE_ADMIN],
                    'links' => [
                        '/admin/block/default/index',
                        '/admin/block/default/grid-sort',
                        '/admin/block/default/grid-page-size',
                    ],
                ],
                'editBlocks' => [
                    'title' => 'Управление блоками',
                    'roles' => [self::ROLE_DEV],
                    'childs' => ['viewBlocks'],
                    'links' => [
                        '/admin/block/default/create',
                        '/admin/block/default/update',
                        '/admin/block/default/delete',
                        '/admin/block/default/bulk-delete',
                    ],
                ],
            ],
        ];
    }
}
