<?php

use artsoft\db\PermissionsMigration;

class m210301_150346_add_auditory_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('auditoryManagement', 'Справочник "Аудитории"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('auditoryManagement');
    }

    public function getPermissions()
    {
        return [
            'auditoryManagement' => [
                'links' => [
                    '/admin/auditory/*',
                    '/admin/auditory/default/*',
                    '/admin/auditory/cat/*',
                    '/admin/auditory/building/*',
                ],
                'viewAuditory' => [
                    'title' => 'Просмотр "Аудитории"',
                    'links' => [
                        '/admin/auditory/default/index',
                        '/admin/auditory/default/view',
                        '/admin/auditory/default/grid-sort',
                        '/admin/auditory/default/grid-page-size',
                        '/admin/auditory/cat/index',
                        '/admin/auditory/cat/view',
                        '/admin/auditory/cat/grid-page-size',
                        '/admin/auditory/building/index',
                        '/admin/auditory/building/view',
                        '/admin/auditory/building/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                ],
                'editAuditory' => [
                    'title' => 'Редактирование записи "Аудитории"',
                    'links' => [
                        '/admin/auditory/default/update',
                        '/admin/auditory/cat/update',
                        '/admin/auditory/building/update',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewAuditory',
                    ],
                ],
                'createAuditory' => [
                    'title' => 'Добавление записи в "Аудитории"',
                    'links' => [
                        '/admin/auditory/default/create',
                        '/admin/auditory/cat/create',
                        '/admin/auditory/building/create',

                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewAuditory',
                    ],
                ],
                'deleteAuditory' => [
                    'title' => 'Удаление записи из "Аудитории"',
                    'links' => [
                        '/admin/auditory/default/delete',
                        '/admin/auditory/default/bulk-delete',
                        '/admin/auditory/cat/delete',
                        '/admin/auditory/cat/bulk-delete',
                        '/admin/auditory/building/delete',
                        '/admin/auditory/building/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'createAuditory',
                    ],
                ],
            ],
        ];
    }

}
