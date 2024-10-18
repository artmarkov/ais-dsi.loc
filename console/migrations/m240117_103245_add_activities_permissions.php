<?php

use artsoft\db\PermissionsMigration;

class m240117_103245_add_activities_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('activitiesManagement', 'Управление разделом "Календарь мероприятий"');
        $this->addRole('activitiesAdmin', 'Администратор раздела "Календарь мероприятий"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('activitiesManagement');
        $this->deleteRole('activitiesAdmin');
    }

    public function getPermissions()
    {
        return [
            'activitiesManagement' => [
                'links' => [
                    '/activities/default/*',
                    '/admin/activities/default/*',
                ],
                'viewActivitiesFrontendStudent' => [
                    'title' => 'Доступ к разделу "Календарь мероприятий учеников"(фронтенд)',
                    'links' => [
                        '/activities/default/create-event',
                        '/activities/student-schedule/index',
                        '/activities/student-schedule/init-calendar',
                    ],
                    'roles' => [
                        self::ROLE_STUDENT,
                        self::ROLE_PARENTS,
                    ],
                ],
                'viewActivitiesFrontend' => [
                    'title' => 'Доступ к разделу "Календарь мероприятий"(фронтенд)',
                    'links' => [
                        '/activities/default/calendar',
                        '/activities/default/init-calendar',
                        '/activities/schoolplan-outside/index',
                        '/activities/schoolplan-outside/init-calendar',
                        '/activities/schoolplan-outside/create-event',
                        '/activities/activities-over/index',
                        '/activities/activities-over/view',
                        '/activities/activities-over/grid-sort',
                        '/activities/activities-over/grid-page-size',
                        '/activities/auditory-schedule/index',
                        '/activities/auditory-schedule/auditories',
                        '/activities/auditory-schedule/init-calendar',
                        '/activities/teachers-schedule/index',
                        '/activities/teachers-schedule/teachers',
                        '/activities/teachers-schedule/init-calendar',
                    ],
                    'roles' => [
                        self::ROLE_EMPLOYEES,
                        self::ROLE_TEACHER,
                    ],
                    'childs' => [
                        'viewActivitiesFrontendStudent',
                    ],
                ],
                'viewActivities' => [
                    'title' => 'Доступ к разделу "Календарь мероприятий"(просмотр)',
                    'links' => [
                        '/admin/activities/default/index',
                        '/admin/activities/default/view',
                        '/admin/activities/default/grid-sort',
                        '/admin/activities/default/grid-page-size',
                        '/admin/activities/default/calendar',
                        '/admin/activities/default/init-calendar',
                        '/admin/activities/default/create-event',
                        '/admin/activities/activities-over/index',
                        '/admin/activities/activities-over/view',
                        '/admin/activities/activities-over/grid-sort',
                        '/admin/activities/activities-over/grid-page-size',
                        '/admin/activities/schoolplan-outside/index',
                        '/admin/activities/schoolplan-outside/init-calendar',
                        '/admin/activities/schoolplan-outside/create-event',
                        '/admin/activities/auditory-schedule/index',
                        '/admin/activities/auditory-schedule/auditories',
                        '/admin/activities/auditory-schedule/init-calendar',
                        '/admin/activities/teachers-schedule/index',
                        '/admin/activities/teachers-schedule/teachers',
                        '/admin/activities/teachers-schedule/init-calendar',
                        '/admin/activities/student-schedule/index',
                        '/admin/activities/student-schedule/init-calendar',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'editActivities' => [
                    'title' => 'Доступ к разделу "Календарь мероприятий"(редактирование)',
                    'links' => [
                        '/admin/activities/default/update',
                        '/admin/activities/default/delete',
                        '/admin/activities/default/refactor-event',
                        '/admin/activities/default/update-event',
                        '/admin/activities/default/delete-event',
                        '/admin/activities/activities-over/update',
                        '/admin/activities/activities-over/delete',
                        '/admin/activities/schoolplan-outside/refactor-event',
                        '/admin/activities/schoolplan-outside/update-event',
                        '/admin/activities/schoolplan-outside/delete-event',
                        '/admin/activities/auditory-schedule/refactor-event',
                        '/admin/activities/auditory-schedule/update-event',
                        '/admin/activities/auditory-schedule/delete-event',
                        '/admin/activities/teachers-schedule/refactor-event',
                        '/admin/activities/teachers-schedule/update-event',
                        '/admin/activities/teachers-schedule/delete-event',
                        '/admin/activities/student-schedule/refactor-event',
                        '/admin/activities/student-schedule/update-event',
                        '/admin/activities/student-schedule/delete-event',
                    ],
                    'roles' => [
                        'activitiesAdmin',
                    ],
                    'childs' => [
                        'viewActivities',
                    ],
                ],
            ],
        ];
    }

}
