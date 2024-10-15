<?php

use artsoft\db\PermissionsMigration;

class m241010_165245_add_education_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('educationManagement', 'Управление разделом "Образовательные программы"');
        $this->addRole('educationAdmin', 'Администратор раздела "Образовательные программы"');
        $this->addRole('educationPreregistrationAdmin', 'Администратор подраздела "Программы для предварительной записи"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('educationManagement');
        $this->deleteRole('educationAdmin');
        $this->deleteRole('educationPreregistrationAdmin');
    }

    public function getPermissions()
    {
        return [
            'educationManagement' => [
                'links' => [
                    '/admin/education/default/*',
                    '/admin/education/entrant-programm/*',
                ],
                'viewEducation' => [
                    'title' => 'Просмотр раздела "Образовательные программы"',
                    'links' => [
                        '/admin/education/default/index',
                        '/admin/education/default/view',
                        '/admin/education/default/grid-sort',
                        '/admin/education/default/grid-page-size',
                        '/admin/education/entrant-programm/index',
                        '/admin/education/entrant-programm/view',
                        '/admin/education/entrant-programm/grid-sort',
                        '/admin/education/entrant-programm/grid-page-size',

                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'editEducation' => [
                    'title' => 'Полный доступ к разделу "Образовательные программы"',
                    'links' => [
                        '/admin/education/default/create',
                        '/admin/education/default/update',
                        '/admin/education/default/history',
                        '/admin/education/default/delete',
                        '/admin/education/default/bulk-activate',
                        '/admin/education/default/bulk-deactivate',
                        '/admin/education/default/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                        'educationAdmin',
                    ],
                    'childs' => [
                        'viewEducation',
                    ],
                ],
                'editEducationPreregistration' => [
                    'title' => 'Полный доступ к разделу "Программы для предварительной записи"',
                    'links' => [
                        '/admin/education/entrant-programm/create',
                        '/admin/education/entrant-programm/update',
                        '/admin/education/entrant-programm/history',
                        '/admin/education/entrant-programm/delete',
                        '/admin/education/entrant-programm/bulk-activate',
                        '/admin/education/entrant-programm/bulk-deactivate',
                        '/admin/education/entrant-programm/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                        'educationPreregistrationAdmin',
                    ],
                    'childs' => [
                        'viewEducation',
                    ],
                ],
            ],
        ];
    }

}
