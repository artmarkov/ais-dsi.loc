<?php

use artsoft\db\PermissionsMigration;

class m231013_113246_add_parents_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('parentsManagement', 'Личный кабинет родителя');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('parentsManagement');
    }

    public function getPermissions()
    {
        return [
            'parentsManagement' => [
                'links' => [
                    '/parents/default/*',
                ],
                'accessParentsFrontend' => [
                    'title' => 'Доступ к личному кабинету родителя(frontend)',
                    'links' => [
                        '/parents/default/index',
                        '/parents/studyplan/grid-sort',
                        '/parents/studyplan/grid-page-size',
                        '/parents/studyplan/index',
                        '/parents/studyplan/view',
                        '/parents/studyplan/schedule-items',
                        '/parents/studyplan/schedule-items/grid-sort',
                        '/parents/studyplan/schedule-items/grid-page-size',
                        '/parents/studyplan/schedule',
                        '/parents/studyplan/consult-items',
                        '/parents/studyplan/consult-items/grid-sort',
                        '/parents/studyplan/consult-items/grid-page-size',
                        '/parents/studyplan/characteristic-items',
                        '/parents/studyplan/characteristic-items/grid-sort',
                        '/parents/studyplan/characteristic-items/grid-page-size',
                        '/parents/studyplan/thematic-items',
                        '/parents/studyplan/thematic-items/grid-sort',
                        '/parents/studyplan/thematic-items/grid-page-size',
                        '/parents/studyplan/thematic-items?mode=view',
                        '/parents/studyplan/studyplan-progress',
                        '/parents/studyplan/studyplan-progress/grid-sort',
                        '/parents/studyplan/studyplan-progress/grid-page-size',
                        '/parents/studyplan/studyplan-progress?mode=view',
                        '/parents/studyplan/studyplan-invoices',
                        '/parents/studyplan/studyplan-invoices?mode=view',
                        '/parents/studyplan/make-invoices',
                        '/parents/studyplan/studyplan-invoices/grid-sort',
                        '/parents/studyplan/studyplan-invoices/grid-page-size',
                        '/parents/studyplan/studyplan-perform',
                        '/parents/studyplan/studyplan-perform/grid-sort',
                        '/parents/studyplan/studyplan-perform/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_PARENTS,
                    ],
                ],
            ],
        ];
    }

}
