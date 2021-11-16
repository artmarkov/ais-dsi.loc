<?php

use artsoft\db\PermissionsMigration;

class m211111_150556_add_help_permission extends PermissionsMigration
{
    public function beforeUp()
    {
        $this->addPermissionsGroup('helpManagement', 'Управление руководством');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('helpManagement');
    }

    public function getPermissions()
    {
        return [
            'helpManagement' => [
                'links' => [
                    '/help/*',
                    '/help/support/*',
                    '/help/guide-help/*',
                    '/admin/guidesys/help-tree/*',
                ],
                'viewHelp' => [
                    'title' => 'Просмотр руководства',
                    'links' => [
                        '/help/guide-help/index',
                        '/help/guide-help/check',
                    ],
                    'roles' => [
                        self::ROLE_USER,
                        self::ROLE_STUDENT,
                        self::ROLE_PARENTS,
                    ],
                ],
                'editHelp' => [
                    'title' => 'Редактирование руководства',
                    'links' => [
                        '/admin/guidesys/guide-help/index',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewHelp',
                    ],
                ],
                'viewHelpAbout' => [
                    'title' => 'Просмотр сведений об АИС',
                    'links' => [
                        '/help/support/about',
                    ],
                    'roles' => [
                        self::ROLE_USER,
                    ],
                ],
                'viewHelpSupport' => [
                    'title' => 'Обращение в службу поддержки',
                    'links' => [
                        '/help/support/index',
                    ],
                    'roles' => [
                        self::ROLE_USER,
                    ],
                ],
            ],
        ];
    }
}
