<?php

use artsoft\db\PermissionsMigration;

class m230606_153245_add_preregistration_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('preregistrationManagement', 'Управление Предварительной записью');
        $this->addRole('preregistrationAdmin', 'Администратор Предварительной записи');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('preregistrationManagement');
        $this->deleteRole('preregistrationAdmin');
    }

    public function getPermissions()
    {
        return [
            'preregistrationManagement' => [
                'links' => [
                    '/admin/preregistration/default/*',
                ],
                'viewPreregistration' => [
                    'title' => 'Просмотр Предварительной записи',
                    'links' => [
                        '/admin/preregistration/default/index',
                        '/admin/preregistration/default/view',
                        '/admin/preregistration/default/grid-sort',
                        '/admin/preregistration/default/grid-page-size',

                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'editPreregistration' => [
                    'title' => 'Редактирование Предварительной записи',
                    'links' => [
                        '/admin/preregistration/default/create',
                        '/admin/preregistration/default/update',
                        '/admin/preregistration/default/delete',
                        '/admin/preregistration/default/bulk-delete',
                    ],
                    'roles' => [
                        'preregistrationAdmin',
                    ],
                    'childs' => [
                        'viewPreregistration',
                    ],
                ],
            ],
        ];
    }

}
