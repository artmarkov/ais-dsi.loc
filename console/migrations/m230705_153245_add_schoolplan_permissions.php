<?php

use artsoft\db\PermissionsMigration;

class m230705_153245_add_schoolplan_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('schoolplanManagement', 'Управление Планом работы');
        $this->addRole('schoolplanAdmin', 'Администратор Плана работы');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('schoolplanManagement');
        $this->deleteRole('schoolplanAdmin');
    }

    public function getPermissions()
    {
        return [
            'schoolplanManagement' => [
                'links' => [
                    '/schoolplan/default/*',
                    '/schoolplan/default/perform/*',
                    '/schoolplan/default/protocol/*',
                    '/schoolplan/default/teachers-efficiency/*',
                    '/admin/schoolplan/default/*',
                    '/admin/schoolplan/default/perform/*',
                    '/admin/schoolplan/default/protocol/*',
                    '/admin/schoolplan/default/teachers-efficiency/*',
                ],
                'viewschoolplan' => [
                    'title' => 'Доступ к Плану работы(просмотр)',
                    'links' => [
                        '/schoolplan/default/index',
                        '/schoolplan/default/view',
                        '/schoolplan/default/grid-sort',
                        '/schoolplan/default/grid-page-size',
                        '/schoolplan/default/perform',
                        '/schoolplan/default/perform?mode=view',
                        '/schoolplan/default/perform?mode=update',
                        '/schoolplan/default/perform?mode=create',
                        '/schoolplan/default/perform?mode=delete',
                        '/schoolplan/default/protocol',
                        '/schoolplan/default/protocol?mode=view',
                        '/schoolplan/default/protocol?mode=update',
                        '/schoolplan/default/protocol?mode=create',
                        '/schoolplan/default/protocol?mode=delete',
                        '/schoolplan/default/studyplan',
                        '/schoolplan/default/studyplan-perform',
                        '/schoolplan/default/executors-perform',
                        '/schoolplan/default/studyplan-subject',
                        '/schoolplan/default/studyplan-subject-perform',
                        '/schoolplan/default/studyplan-thematic',
                        '/schoolplan/default/studyplan-thematic-perform',
                        '/schoolplan/default/studyplan-subject-teachers-perform'
                    ],
                    'roles' => [
                        self::ROLE_TEACHER,
                    ],
                ],
                'editschoolplan' => [
                    'title' => 'Доступ к Плану работы(редактирование)',
                    'links' => [
                        '/schoolplan/default/create',
                        '/schoolplan/default/update',
                        '/schoolplan/default/delete',
                        '/schoolplan/default/clone',
                    ],
                    'roles' => [
                        self::ROLE_DEPARTMENT,
                    ],
                    'childs' => [
                        'viewschoolplan',
                    ],
                ],
                'fullschoolplanAccess' => [
                    'title' => 'Администрирование Плана работы',
                    'links' => [
                        '/admin/schoolplan/default/index',
                        '/admin/schoolplan/default/view',
                        '/admin/schoolplan/default/history',
                        '/admin/schoolplan/default/create',
                        '/admin/schoolplan/default/update',
                        '/admin/schoolplan/default/delete',
                        '/admin/schoolplan/default/clone',
                        '/admin/schoolplan/default/grid-sort',
                        '/admin/schoolplan/default/grid-page-size',
                        '/admin/schoolplan/default/perform',
                        '/admin/schoolplan/default/perform?mode=create',
                        '/admin/schoolplan/default/perform?mode=view',
                        '/admin/schoolplan/default/perform?mode=update',
                        '/admin/schoolplan/default/perform?mode=delete',
                        '/admin/schoolplan/default/perform?mode=history',
                        '/admin/schoolplan/default/protocol',
                        '/admin/schoolplan/default/protocol?mode=create',
                        '/admin/schoolplan/default/protocol?mode=view',
                        '/admin/schoolplan/default/protocol?mode=update',
                        '/admin/schoolplan/default/protocol?mode=delete',
                        '/admin/schoolplan/default/protocol?mode=history',
                        '/admin/schoolplan/default/studyplan',
                        '/admin/schoolplan/default/studyplan-subject',
                        '/admin/schoolplan/default/studyplan-thematic',
                        '/admin/schoolplan/default/executors-perform',
                        '/admin/schoolplan/default/studyplan-perform',
                        '/admin/schoolplan/default/studyplan-subject-perform',
                        '/admin/schoolplan/default/studyplan-subject-teachers-perform',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                       'schoolplanAdmin',
                    ],
                ],
            ],
        ];
    }

}
