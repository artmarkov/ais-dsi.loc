<?php

use artsoft\db\PermissionsMigration;

class m230605_123245_add_service_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('serviceManagement', 'Управление СКУД');
        $this->addRole('serviceAdmin', 'Администратор СКУД');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('serviceManagement');
        $this->deleteRole('serviceAdmin');
    }

    public function getPermissions()
    {
        return [
            'serviceManagement' => [
                'links' => [
                    '/service/default/*',
                    '/service/attendlog/*',
                    '/service/sigur/*',
                    '/admin/service/default/*',
                    '/admin/service/attendlog/*',
                    '/admin/service/sigur/*',
                ],
                'viewService' => [
                    'title' => 'Администрирование сервисов СКУД',
                    'links' => [
                        '/service/default/index',
                        '/service/default/create',
                        '/service/default/update',
                        '/service/default/delete',
                        '/service/default/grid-sort',
                        '/service/default/grid-page-size',
                        '/service/attendlog/index',
                        '/service/attendlog/create',
                        '/service/attendlog/update',
                        '/service/attendlog/find',
                        '/service/attendlog/over',
                        '/service/attendlog/delete',
                        '/service/attendlog/grid-sort',
                        '/service/attendlog/grid-page-size',
                        '/service/sigur/index',
                        '/service/sigur/view',
                        '/service/sigur/grid-sort',
                        '/service/sigur/grid-page-size',
                    ],
                    'roles' => [
                        'serviceAdmin',
                    ],
                ],
                'fullServiceAccess' => [
                    'title' => 'Полный доступ к СКУД',
                    'links' => [
                        '/admin/service/default/index',
                        '/admin/service/default/create',
                        '/admin/service/default/update',
                        '/admin/service/default/delete',
                        '/admin/service/default/history',
                        '/admin/service/default/grid-sort',
                        '/admin/service/default/grid-page-size',
                        '/admin/service/attendlog/index',
                        '/admin/service/attendlog/create',
                        '/admin/service/attendlog/update',
                        '/admin/service/attendlog/find',
                        '/admin/service/attendlog/over',
                        '/admin/service/attendlog/delete',
                        '/admin/service/attendlog/grid-sort',
                        '/admin/service/attendlog/grid-page-size',
                        '/admin/service/sigur/index',
                        '/admin/service/sigur/view',
                        '/admin/service/sigur/delete',
                        '/admin/service/sigur/grid-sort',
                        '/admin/service/sigur/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
            ],
        ];
    }

}
