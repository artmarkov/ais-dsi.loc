<?php

use artsoft\db\PermissionsMigration;

class m210301_151105_add_student_guide_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('guidestudyManagement', 'Справочник "Учебные справочники"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('guidestudyManagement');
    }

    public function getPermissions()
    {
        return [
            'guidestudyManagement' => [
                'links' => [
                    '/admin/guidestudy/*',
                    '/admin/guidestudy/default/*',
                    '/admin/guidestudy/education-cat/*',
                    '/admin/guidestudy/education-level/*',
                ],
                'viewGuidestudy' => [
                    'title' => 'Просмотр "Учебные справочники"',
                    'links' => [
                        '/admin/guidestudy/default/index',
                        '/admin/guidestudy/default/view',
                        '/admin/guidestudy/default/grid-page-size',
                        '/admin/guidestudy/education-cat/index',
                        '/admin/guidestudy/education-cat/view',
                        '/admin/guidestudy/education-cat/grid-page-size',
                        '/admin/guidestudy/education-level/index',
                        '/admin/guidestudy/education-level/view',
                        '/admin/guidestudy/education-level/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                ],
                'editGuidestudy' => [
                    'title' => 'Редактирование записи "Учебные справочники"',
                    'links' => [
                        '/admin/guidestudy/default/update',
                        '/admin/guidestudy/education-cat/update',
                        '/admin/guidestudy/education-level/update',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewGuidestudy',
                    ],
                ],
                'createGuidestudy' => [
                    'title' => 'Добавление записи в "Учебные справочники"',
                    'links' => [
                        '/admin/guidestudy/default/create',
                        '/admin/guidestudy/education-cat/create',
                        '/admin/guidestudy/education-level/create',

                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewGuidestudy',
                    ],
                ],
                'deleteGuidestudy' => [
                    'title' => 'Удаление записи из "Учебные справочники"',
                    'links' => [
                        '/admin/guidestudy/default/delete',
                        '/admin/guidestudy/default/bulk-delete',
                        '/admin/guidestudy/education-cat/delete',
                        '/admin/guidestudy/education-cat/bulk-delete',
                        '/admin/guidestudy/education-level/delete',
                        '/admin/guidestudy/education-level/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'createGuidestudy',
                    ],
                ],
            ],
        ];
    }

}
