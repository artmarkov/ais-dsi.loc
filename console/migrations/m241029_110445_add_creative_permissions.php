<?php

use artsoft\db\PermissionsMigration;

class m241029_110445_add_creative_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('creativeManagement', 'Управление разделом "Работы и сертификаты"');
        $this->addRole('creativeAdmin', 'Администратор раздела "Работы и сертификаты"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('creativeManagement');
        $this->deleteRole('creativeAdmin');
    }

    public function getPermissions()
    {
        return [
            'creativeManagement' => [
                'links' => [
                    '/admin/creative/default/*',
                    '/admin/creative/category/*',
                ],
                
                'viewCreative' => [
                    'title' => 'Доступ к разделу "Работы и сертификаты"(просмотр)',
                    'links' => [
                        '/admin/creative/default/index',
                        '/admin/creative/default/view',
                        '/admin/creative/default/grid-sort',
                        '/admin/creative/default/grid-page-size',
                        '/admin/creative/category/index',
                        '/admin/creative/category/grid-sort',
                        '/admin/creative/category/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'editCreative' => [
                    'title' => 'Доступ к разделу "Работы и сертификаты"(редактирование)',
                    'links' => [
                        '/admin/creative/default/update',
                        '/admin/creative/default/delete',
                        '/admin/creative/default/bulk-activate',
                        '/admin/creative/default/bulk-deactivate',
                        '/admin/creative/default/bulk-delete',
                        '/admin/creative/category/update',
                        '/admin/creative/category/delete',
                        '/admin/creative/category/bulk-delete',

                    ],
                    'roles' => [
                        'creativeAdmin',
                    ],
                    'childs' => [
                        'viewCreative',
                    ],
                ],
            ],
        ];
    }

}
