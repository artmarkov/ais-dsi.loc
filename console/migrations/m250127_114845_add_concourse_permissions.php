<?php

use artsoft\db\PermissionsMigration;

class m250127_114845_add_concourse_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('concourseManagement', 'Управление разделом "Конкурсы"');
        $this->addRole('concourseAdmin', 'Администратор раздела "Конкурсы"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('concourseManagement');
        $this->deleteRole('concourseAdmin');
    }

    public function getPermissions()
    {
        return [
            'concourseManagement' => [
                'links' => [
                    '/admin/concourse/default/*',
                    '/admin/concourse/default/concourse-criteria/*',
                    '/admin/concourse/default/concourse-item/*',
                    '/admin/concourse/default/concourse-answers/*',
                ],
                'viewConcourseBackend' => [
                    'title' => 'Просмотр карточек "Конкурсы"(backend)',
                    'links' => [
                        '/admin/concourse/default/index',
                        '/admin/concourse/default/grid-sort',
                        '/admin/concourse/default/grid-page-size',
                        '/admin/concourse/default/concourse-criteria',
                        '/admin/concourse/default/concourse-item',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'editConcourseBackend' => [
                    'title' => 'Редактирование карточек "Конкурсы"(backend)',
                    'links' => [
                        '/admin/concourse/default/create',
                        '/admin/concourse/default/update',
                        '/admin/concourse/default/delete',
                        '/admin/concourse/default/concourse-criteria?mode=create',
                        '/admin/concourse/default/concourse-criteria?mode=update',
                        '/admin/concourse/default/concourse-criteria?mode=delete',
                        '/admin/concourse/default/concourse-item?mode=create',
                        '/admin/concourse/default/concourse-item?mode=update',
                        '/admin/concourse/default/concourse-item?mode=delete',
                        '/admin/concourse/default/concourse-answers?mode=update',
                        '/admin/concourse/default/concourse-answers?mode=delete',
                        '/admin/concourse/default/set-mark',
                        '/admin/concourse/default/stat',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                        'concourseAdmin'
                    ],
                    'childs' => [
                        'viewConcourseBackend',
                    ],
                ],
            ],
        ];
    }

}
