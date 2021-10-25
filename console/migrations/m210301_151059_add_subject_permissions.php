<?php

use artsoft\db\PermissionsMigration;

class m210301_151059_add_subject_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('subjectManagement', 'Справочник "Дисциплины"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('subjectManagement');
    }

    public function getPermissions()
    {
        return [
            'subjectManagement' => [
                'links' => [
                    '/admin/subject/*',
                    '/admin/subject/default/*',
                    '/admin/subject/category/*',
                    '/admin/subject/type/*',
                    '/admin/subject/vid/*',
                ],
                'viewSubject' => [
                    'title' => 'Просмотр "Дисциплины"',
                    'links' => [
                        '/admin/subject/default/index',
                        '/admin/subject/default/view',
                        '/admin/subject/default/grid-page-size',
                        '/admin/subject/category/index',
                        '/admin/subject/category/view',
                        '/admin/subject/category/grid-sort',
                        '/admin/subject/category/grid-page-size',
                        '/admin/subject/type/index',
                        '/admin/subject/type/view',
                        '/admin/subject/type/grid-page-size',
                        '/admin/subject/vid/index',
                        '/admin/subject/vid/view',
                        '/admin/subject/vid/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                ],
                'editSubject' => [
                    'title' => 'Редактирование записи "Дисциплины"',
                    'links' => [
                        '/admin/subject/default/update',
                        '/admin/subject/category/update',
                        '/admin/subject/type/update',
                        '/admin/subject/vid/update',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewSubject',
                    ],
                ],
                'createSubject' => [
                    'title' => 'Добавление записи в "Дисциплины"',
                    'links' => [
                        '/admin/subject/default/create',
                        '/admin/subject/category/create',
                        '/admin/subject/type/create',
                        '/admin/subject/vid/create',

                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewSubject',
                    ],
                ],
                'deleteSubject' => [
                    'title' => 'Удаление записи из "Дисциплины"',
                    'links' => [
                        '/admin/subject/default/delete',
                        '/admin/subject/default/bulk-delete',
                        '/admin/subject/category/delete',
                        '/admin/subject/category/bulk-delete',
                        '/admin/subject/type/delete',
                        '/admin/subject/type/bulk-delete',
                        '/admin/subject/vid/delete',
                        '/admin/subject/vid/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'createSubject',
                    ],
                ],
            ],
        ];
    }

}
