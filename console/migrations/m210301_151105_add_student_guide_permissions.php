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
                    '/admin/guidestudy/lesson-mark/*',
                    '/admin/guidestudy/lesson-test/*',
                    '/admin/guidestudy/entrant-test/*',
                    '/admin/guidestudy/cost-education/*',
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
                        '/admin/guidestudy/lesson-mark/index',
                        '/admin/guidestudy/lesson-mark/view',
                        '/admin/guidestudy/lesson-mark/grid-page-size',
                        '/admin/guidestudy/lesson-test/index',
                        '/admin/guidestudy/lesson-test/view',
                        '/admin/guidestudy/lesson-test/grid-page-size',
                        '/admin/guidestudy/entrant-test/index',
                        '/admin/guidestudy/entrant-test/view',
                        '/admin/guidestudy/entrant-test/grid-page-size',
                        '/admin/guidestudy/cost-education/index',
                        '/admin/guidestudy/cost-education/view',
                        '/admin/guidestudy/cost-education/grid-page-size',
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
                        '/admin/guidestudy/lesson-mark/update',
                        '/admin/guidestudy/lesson-test/update',
                        '/admin/guidestudy/entrant-test/update',
                        '/admin/guidestudy/cost-education/update',
                        '/admin/guidestudy/cost-education/set-standart-basic',
                        '/admin/guidestudy/cost-education/set-basic-ratio',
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
                        '/admin/guidestudy/lesson-mark/create',
                        '/admin/guidestudy/lesson-test/create',
                        '/admin/guidestudy/entrant-test/create',
                        '/admin/guidestudy/cost-education/create',

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
                        '/admin/guidestudy/lesson-mark/delete',
                        '/admin/guidestudy/lesson-mark/bulk-delete',
                        '/admin/guidestudy/lesson-test/delete',
                        '/admin/guidestudy/lesson-test/bulk-delete',
                        '/admin/guidestudy/entrant-test/delete',
                        '/admin/guidestudy/entrant-test/bulk-delete',
                        '/admin/guidestudy/cost-education/delete',
                        '/admin/guidestudy/cost-education/bulk-delete',
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
