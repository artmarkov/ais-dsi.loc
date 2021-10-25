<?php

use artsoft\db\PermissionsMigration;

class m211020_123250_add_systen_guide_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('guidesysManagement', 'Справочник "Системные справочники"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('guidesysManagement');
    }

    public function getPermissions()
    {
        return [
            'guidesysManagement' => [
                'links' => [
                    '/admin/guidesys/*',
                    '/admin/guidesys/default/*',
                    '/admin/guidesys/activities-cat/*',
                    '/admin/guidesys/routine-cat/*',
                    '/admin/guidesys/efficiency-tree/*',
                    '/admin/guidesys/treemanager/node/*',
                ],
                'viewGuidesys' => [
                    'title' => 'Просмотр "Системные справочники"',
                    'links' => [
                        '/admin/guidesys/default/index',
                        '/admin/guidesys/default/view',
                        '/admin/guidesys/default/grid-page-size',
                        '/admin/guidesys/activities-cat/index',
                        '/admin/guidesys/activities-cat/view',
                        '/admin/guidesys/activities-cat/grid-page-size',
                        '/admin/guidesys/routine-cat/index',
                        '/admin/guidesys/routine-cat/view',
                        '/admin/guidesys/routine-cat/grid-page-size',
                        '/admin/guidesys/efficiency-tree/index',
                        '/admin/treemanager/node/move',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                ],
                'editGuidesys' => [
                    'title' => 'Редактирование записи "Системные справочники"',
                    'links' => [
                        '/admin/guidesys/default/update',
                        '/admin/guidesys/activities-cat/update',
                        '/admin/guidesys/routine-cat/update',
                        '/admin/treemanager/node/save',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewGuidesys',
                    ],
                ],
                'createGuidesys' => [
                    'title' => 'Добавление записи в "Системные справочники"',
                    'links' => [
                        '/admin/guidesys/default/create',
                        '/admin/guidesys/activities-cat/create',
                        '/admin/guidesys/routine-cat/create',
                        '/admin/treemanager/node/manage',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewGuidesys',
                    ],
                ],
                'deleteGuidesys' => [
                    'title' => 'Удаление записи из "Системные справочники"',
                    'links' => [
                        '/admin/guidesys/default/delete',
                        '/admin/guidesys/default/bulk-delete',
                        '/admin/guidesys/activities-cat/delete',
                        '/admin/guidesys/activities-cat/bulk-delete',
                        '/admin/guidesys/routine-cat/delete',
                        '/admin/guidesys/routine-cat/bulk-delete',
                        '/admin/treemanager/node/remove',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'createGuidesys',
                    ],
                ],
            ],
        ];
    }

}
