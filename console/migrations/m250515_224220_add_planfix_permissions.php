<?php

use artsoft\db\PermissionsMigration;

class m250515_224220_add_planfix_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('planfixManagement', 'Планировщик задач');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('planfixManagement');
    }

    public function getPermissions()
    {
        return [
            'planfixManagement' => [
                'links' => [
                    '/admin/planfix/*',
                    '/admin/planfix/default/*',
                ],
                'viewPlanfixs' => [
                    'title' => 'Просмотр задач',
                    'roles' => [self::ROLE_ADMIN],
                    'links' => [
                        '/admin/planfix/default/index',
                        '/admin/planfix/default/grid-sort',
                        '/admin/planfix/default/grid-page-size',
                    ],
                ],
                'editPlanfixs' => [
                    'title' => 'Управление задачами',
                    'roles' => [self::ROLE_ADMIN],
                    'childs' => ['viewPlanfixs'],
                    'links' => [
                        '/admin/planfix/default/create',
                        '/admin/planfix/default/update',
                    ],
                ],
                'delPlanfixs' => [
                    'title' => 'Удаление задач',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewPlanfixs'],
                    'links' => [
                        '/admin/planfix/default/delete',
                        '/admin/planfix/default/bulk-delete',
                    ],
                ],

            ],
        ];
    }
}
