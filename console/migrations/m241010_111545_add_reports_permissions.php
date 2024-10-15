<?php

use artsoft\db\PermissionsMigration;

class m241010_111545_add_reports_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('reportsManagement', 'Управление разделом "Отчеты"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('reportsManagement');
    }

    public function getPermissions()
    {
        return [
            'reportsManagement' => [
                'links' => [
                    '/admin/reports/default/*',
                ],
                'reportsAccess' => [
                    'title' => 'Доступ к разделу "Отчеты"',
                    'links' => [
                        '/admin/reports/default/index',
                        '/admin/reports/default/tarif-statement',
                        '/admin/reports/default/studyplan-stat',
                        '/admin/reports/default/teachers-schedule',
                        '/admin/reports/default/generator-schedule',
                        '/admin/reports/summary-progress/index',
                        '/admin/reports/working-time/index',
                        '/admin/reports/working-time/summary',
                        '/admin/reports/working-time/bar',
                    ],
                    'roles' => [
                        self::ROLE_ADMIN,
                        self::ROLE_SYSTEM,
                    ],
                ],
            ],
        ];
    }

}
