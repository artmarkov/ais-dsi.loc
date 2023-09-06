<?php

use artsoft\db\PermissionsMigration;

class m230823_193246_add_studyplan_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('studyplanManagement', 'Личный кабинет ученика');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('studyplanManagement');
    }

    public function getPermissions()
    {
        return [
            'studyplanManagement' => [
                'links' => [
                    '/student/default/*',
                    '/studyplan/default/*',
                ],
                'accessStudyplanFrontend' => [
                    'title' => 'Доступ к личному кабинету ученика',
                    'links' => [
                        '/student/default/index',
                        '/student/default/students-view',
                        '/studyplan/default/index',
                        '/studyplan/default/view',
                        '/studyplan/default/grid-sort',
                        '/studyplan/default/grid-page-size',
                        '/studyplan/default/schedule-items',
                        '/studyplan/default/schedule-items/grid-sort',
                        '/studyplan/default/schedule-items/grid-page-size',
                        '/studyplan/default/schedule',
                        '/studyplan/default/consult-items',
                        '/studyplan/default/consult-items/create',
                        '/studyplan/default/consult-items/update',
                        '/studyplan/default/consult-items/delete',
                        '/studyplan/default/consult-items/grid-sort',
                        '/studyplan/default/consult-items/grid-page-size',
                        '/studyplan/default/studyplan-progress',
                        '/studyplan/default/studyplan-progress/grid-sort',
                        '/studyplan/default/studyplan-progress/grid-page-size',
                        '/studyplan/default/characteristic-items',
                        '/studyplan/default/characteristic-items/grid-sort',
                        '/studyplan/default/characteristic-items/grid-page-size',
                        '/studyplan/default/thematic-items',
                        '/studyplan/default/thematic-items/grid-sort',
                        '/studyplan/default/thematic-items/grid-page-size',
                        '/studyplan/default/thematic-items/mode=view',
                        '/studyplan/default/studyplan-invoices',
                        '/studyplan/default/studyplan-invoices/grid-sort',
                        '/studyplan/default/studyplan-invoices/grid-page-size',
                        '/studyplan/default/studyplan-perform',
                        '/studyplan/default/studyplan-perform/grid-sort',
                        '/studyplan/default/studyplan-perform/grid-page-size',
                        '/studyplan/default/make-invoices',

                    ],
                    'roles' => [
                        self::ROLE_STUDENT,
                    ],
                ],

            ],
        ];
    }

}
