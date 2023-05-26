<?php

use artsoft\db\PermissionsMigration;

class m230525_123245_add_entrant_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('entrantManagement', 'Управление испытаниями');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('entrantManagement');
    }

    public function getPermissions()
    {
        return [
            'entrantManagement' => [
                'links' => [
                    '/entrant/default/*',
                    '/entrant/default/applicants/*',
                    '/admin/entrant/default/*',
                    '/admin/entrant/default/group/*',
                    '/admin/entrant/default/applicants/*',
                ],
                'viewEntrant' => [
                    'title' => 'Просмотр испытаний',
                    'links' => [
                        '/entrant/default/index',
                        '/entrant/default/view',
                        '/entrant/default/grid-sort',
                        '/entrant/default/grid-page-size',
                        '/entrant/default/applicants',
                        '/entrant/default/applicants?mode=view',
                    ],
                    'roles' => [
                        self::ROLE_DEPARTMENT,
                    ],
                ],
                'editEntrant' => [
                    'title' => 'Редактирование испытаний',
                    'links' => [
                        '/entrant/default/applicants?mode=update',
                    ],
                    'roles' => [
                        self::ROLE_DEPARTMENT,
                    ],
                    'childs' => [
                        'viewEntrant',
                    ],
                ],
                'fullEntrantAccess' => [
                    'title' => 'Полный доступ к испытаниям',
                    'links' => [
                        '/admin/entrant/default/index',
                        '/admin/entrant/default/view',
                        '/admin/entrant/default/history',
                        '/admin/entrant/default/grid-sort',
                        '/admin/entrant/default/grid-page-size',
                        '/admin/entrant/default/update',
                        '/admin/entrant/default/create',
                        '/admin/entrant/default/delete',
                        '/admin/entrant/default/bulk-delete',
                        '/admin/entrant/default/group?mode=view',
                        '/admin/entrant/default/group?mode=update',
                        '/admin/entrant/default/group?mode=delete',
                        '/admin/entrant/default/applicants?mode=view',
                        '/admin/entrant/default/applicants?mode=update',
                        '/admin/entrant/default/applicants?mode=delete',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
            ],
        ];
    }

}
