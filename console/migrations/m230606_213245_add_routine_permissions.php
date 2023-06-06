<?php

use artsoft\db\PermissionsMigration;

class m230606_213245_add_routine_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('routineManagement', 'Управление Производственным календарем');
        $this->addRole('routineAdmin', 'Администратор Производственного календаря');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('routineManagement');
        $this->deleteRole('routineAdmin');
    }

    public function getPermissions()
    {
        return [
            'routineManagement' => [
                'links' => [
                    '/admin/routine/default/*',
                ],
                'viewRoutine' => [
                    'title' => 'Просмотр Производственного календаря(backend)',
                    'links' => [
                        '/admin/routine/default/index',
                        '/admin/routine/default/grid-sort',
                        '/admin/routine/default/grid-page-size',
                        '/admin/routine/default/calendar',

                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'viewRoutineCalendar' => [
                    'title' => 'Просмотр Производственного календаря(frontend)',
                    'links' => [
                        '/routine/default/calendar',

                    ],
                    'roles' => [
                        self::ROLE_USER,
                    ],
                ],
                'editRoutine' => [
                    'title' => 'Редактирование Производственного календаря',
                    'links' => [
                        '/admin/routine/default/create',
                        '/admin/routine/default/init-event',
                        '/admin/routine/default/create-event',
                        '/admin/routine/default/update',
                        '/admin/routine/default/delete',
                        '/admin/routine/default/bulk-delete',
                    ],
                    'roles' => [
                        'routineAdmin',
                    ],
                    'childs' => [
                        'viewRoutine',
                    ],
                ],
            ],
        ];
    }

}
