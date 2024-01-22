<?php

use artsoft\db\PermissionsMigration;

class m230903_225246_add_common_frontend_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('commonManagementFront', 'Общий доступ к Фронтенду');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('commonManagementFront');
    }

    public function getPermissions()
    {
        return [
            'commonManagementFront' => [
                'links' => [
                    '/schedule/default/*',
                ],
                'accessScheduleFrontend' => [
                    'title' => 'Доступ к Сетке расписания школы',
                    'links' => [
                        '/schedule/default/index',
                    ],
                    'roles' => [
                        self::ROLE_EMPLOYEES,
                        self::ROLE_TEACHER,
                        self::ROLE_STUDENT,
                        self::ROLE_PARENTS,
                    ],
                ],

            ],
        ];
    }

}
