<?php

use artsoft\db\PermissionsMigration;

class m230606_123245_add_reestr_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('reestrManagement', 'Управление Реестрами');
        $this->addRole('reestrAdmin', 'Администратор Реестров');
        $this->addRole('reestrFrontend', 'Доступ к реестрам Фронтенд');
        $this->addRole('documentAdmin', 'Администратор Документов');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('reestrManagement');
        $this->deleteRole('reestrAdmin');
        $this->deleteRole('reestrFrontend');
        $this->deleteRole('documentAdmin');
    }

    public function getPermissions()
    {
        return [
            'reestrManagement' => [
                'links' => [
                    '/admin/employees/default/*',
                    '/admin/teachers/default/*',
                    '/admin/students/default/*',
                    '/admin/parents/default/*',
                    '/admin/employees/document/*',
                    '/admin/teachers/document/*',
                    '/admin/students/document/*',
                    '/admin/parents/document/*',
                    '/admin/info/document/*',
                ],
                'viewReestr' => [
                    'title' => 'Просмотр Реестров',
                    'links' => [
                        '/admin/info/document/grid-page-size',
                        '/admin/employees/default/index',
                        '/admin/employees/default/view',
                        '/admin/employees/default/grid-sort',
                        '/admin/employees/default/grid-page-size',
                        '/admin/teachers/default/index',
                        '/admin/teachers/default/view',
                        '/admin/teachers/default/grid-sort',
                        '/admin/teachers/default/grid-page-size',
                        '/admin/students/default/index',
                        '/admin/students/default/view',
                        '/admin/students/default/grid-sort',
                        '/admin/students/default/grid-page-size',
                        '/admin/students/default/studyplan',
                        '/admin/parents/default/index',
                        '/admin/parents/default/view',
                        '/admin/parents/default/grid-sort',
                        '/admin/parents/default/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'viewReestrFrontend' => [
                    'title' => 'Просмотр Реестров Фронтенд',
                    'links' => [
                        '/reestr/employees/index',
                        '/reestr/employees/view',
                        '/reestr/employees/grid-sort',
                        '/reestr/employees/grid-page-size',
                        '/reestr/teachers/index',
                        '/reestr/teachers/view',
                        '/reestr/teachers/grid-sort',
                        '/reestr/teachers/grid-page-size',
                        '/reestr/teachers/schedule-items',
                        '/reestr/teachers/schedule',
                        '/reestr/teachers/consult-items',
                        '/reestr/parents/index',
                        '/reestr/parents/view',
                        '/reestr/parents/grid-sort',
                        '/reestr/parents/grid-page-size',
                        '/reestr/studyplan/grid-sort',
                        '/reestr/studyplan/grid-page-size',
                        '/reestr/studyplan/index',
                        '/reestr/studyplan/view',
                        '/reestr/studyplan/students-view',
                        '/reestr/studyplan/schedule-items',
                        '/reestr/studyplan/schedule-items/grid-sort',
                        '/reestr/studyplan/schedule-items/grid-page-size',
                        '/reestr/studyplan/schedule',
                        '/reestr/studyplan/consult-items',
                        '/reestr/studyplan/consult-items/grid-sort',
                        '/reestr/studyplan/consult-items/grid-page-size',
                        '/reestr/studyplan/characteristic-items',
                        '/reestr/studyplan/characteristic-items/grid-sort',
                        '/reestr/studyplan/characteristic-items/grid-page-size',
                        '/reestr/studyplan/thematic-items',
                        '/reestr/studyplan/thematic-items/grid-sort',
                        '/reestr/studyplan/thematic-items/grid-page-size',
                        '/reestr/studyplan/thematic-items?mode=view',
                        '/reestr/studyplan/studyplan-progress',
                        '/reestr/studyplan/studyplan-progress/grid-sort',
                        '/reestr/studyplan/studyplan-progress/grid-page-size',
                        '/reestr/studyplan/studyplan-progress?mode=view',
                        '/reestr/studyplan/studyplan-invoices',
                        '/reestr/studyplan/studyplan-invoices?mode=view',
                        '/reestr/studyplan/studyplan-invoices/grid-sort',
                        '/reestr/studyplan/studyplan-invoices/grid-page-size',
                    ],
                    'roles' => [
                        'reestrFrontend',
                    ],
                ],
                'viewDocument' => [
                    'title' => 'Просмотр Документов',
                    'links' => [
                        '/admin/info/document/index',
                        '/admin/info/document/grid-sort',
                        '/admin/info/document/view',
                        '/admin/employees/default/document',
                        '/admin/employees/default/document?mode=view',
                        '/admin/teachers/default/document',
                        '/admin/teachers/default/document?mode=view',
                        '/admin/students/default/document',
                        '/admin/students/default/document?mode=view',
                        '/admin/parents/default/document',
                        '/admin/parents/default/document?mode=view',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'editReestr' => [
                    'title' => 'Редактирование Реестров',
                    'links' => [
                        '/admin/employees/default/create',
                        '/admin/employees/default/update',
                        '/admin/employees/default/history',
                        '/admin/teachers/default/create',
                        '/admin/teachers/default/update',
                        '/admin/teachers/default/history',
                        '/admin/students/default/finding',
                        '/admin/students/default/registration',
                        '/admin/students/default/create',
                        '/admin/students/default/update',
                        '/admin/students/default/history',
                        '/admin/students/default/studyplan?mode=create',
                        '/admin/students/default/studyplan?mode=update',
                        '/admin/students/default/studyplan?mode=view',
                        '/admin/students/default/studyplan?mode=delete',
                        '/admin/parents/default/create',
                        '/admin/parents/default/update',
                        '/admin/parents/default/history',
                    ],
                    'roles' => [
                        'reestrAdmin',
                    ],
                    'childs' => [
                        'viewReestr',
                    ],
                ],
                'editDocument' => [
                    'title' => 'Редактирование Документов',
                    'links' => [
                        '/admin/info/document/create',
                        '/admin/info/document/update',
                        '/admin/employees/default/document?mode=create',
                        '/admin/employees/default/document?mode=update',
                        '/admin/teachers/default/document?mode=create',
                        '/admin/teachers/default/document?mode=update',
                        '/admin/students/default/document?mode=create',
                        '/admin/students/default/document?mode=update',
                        '/admin/parents/default/document?mode=create',
                        '/admin/parents/default/document?mode=update',
                    ],
                    'roles' => [
                        'documentAdmin',
                    ],
                    'childs' => [
                        'viewDocument',
                    ],
                ],
                'fullReestrAccess' => [
                    'title' => 'Полный доступ к Реестрам',
                    'links' => [
                        '/admin/employees/default/delete',
                        '/admin/employees/default/bulk-delete',
                        '/admin/teachers/default/delete',
                        '/admin/teachers/default/bulk-delete',
                        '/admin/students/default/delete',
                        '/admin/students/default/bulk-delete',
                        '/admin/parents/default/delete',
                        '/admin/parents/default/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'editReestr',
                    ],
                ],
                'fullDocumentAccess' => [
                    'title' => 'Полный доступ к Документам',
                    'links' => [
                        '/admin/info/document/delete',
                        '/admin/info/document/bulk-delete',
                        '/admin/employees/default/document?mode=delete',
                        '/admin/teachers/default/document?mode=delete',
                        '/admin/students/default/document?mode=delete',
                        '/admin/parents/default/document?mode=delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'editDocument',
                    ],
                ],
            ],
        ];
    }

}
