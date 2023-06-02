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
                    'title' => 'Участники экзаменационной комиссии',
                    'links' => [
                        '/entrant/default/index',
                        '/entrant/default/view',
                        '/entrant/default/grid-sort',
                        '/entrant/default/grid-page-size',
                        '/entrant/default/applicants',
                        '/entrant/default/applicants?mode=update',
                    ],
                    'roles' => [
                        self::ROLE_DEPARTMENT,
                    ],
                ],
                'adminEntrant' => [
                    'title' => 'Администратор экзаменационной комиссии',
                    'links' => [
                        '/entrant/default/applicants?mode=activate',
                        '/entrant/default/applicants?mode=deactivate',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                    'childs' => [
                        'viewEntrant',
                    ],
                ],
                'fullEntrantAccess' => [
                    'title' => 'Полный доступ к экзаменам',
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
                        '/admin/entrant/default/group',
                        '/admin/entrant/default/group?mode=create',
                        '/admin/entrant/default/group?mode=view',
                        '/admin/entrant/default/group?mode=update',
                        '/admin/entrant/default/group?mode=delete',
                        '/admin/entrant/default/group?mode=history',
                        '/admin/entrant/default/applicants',
                        '/admin/entrant/default/applicants?mode=create',
                        '/admin/entrant/default/applicants?mode=view',
                        '/admin/entrant/default/applicants?mode=update',
                        '/admin/entrant/default/applicants?mode=delete',
                        '/admin/entrant/default/applicants?mode=history',
                        '/admin/entrant/default/applicants?mode=activate',
                        '/admin/entrant/default/applicants?mode=deactivate',
                        '/admin/entrant/default/protocol',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
            ],
        ];
    }

}
