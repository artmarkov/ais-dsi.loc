<?php

use artsoft\db\PermissionsMigration;

class m150821_140141_add_core_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('dashboard', 'Главная панель');
        $this->addPermissionsGroup('userCommonPermissions', 'Общий доступ');

        $this->addRole(self::ROLE_DEV, 'Разработчик');
        $this->addRole(self::ROLE_SYSTEM, 'Системный администратор');
        $this->addRole(self::ROLE_ADMIN, 'Администратор');
        $this->addRole(self::ROLE_USER, 'Пользователь');
        $this->addRole(self::ROLE_DEPARTMENT, 'Руководитель отдела');
        $this->addRole(self::ROLE_TEACHER, 'Преподаватель');
        $this->addRole(self::ROLE_EMPLOYEES, 'Сотрудник');
        $this->addRole(self::ROLE_STUDENT, 'Ученик');
        $this->addRole(self::ROLE_PARENTS, 'Родитель');

        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'employees', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'teacher', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'department', 'child' => 'teacher']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'student']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'parents']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'system', 'child' => 'administrator']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'developer', 'child' => 'system']);
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('dashboard');
        $this->deletePermissionsGroup('userCommonPermissions');

        $this->deleteRole(self::ROLE_ADMIN);
        $this->deleteRole(self::ROLE_SYSTEM);
        $this->deleteRole(self::ROLE_DEV);
        $this->deleteRole(self::ROLE_USER);
        $this->deleteRole(self::ROLE_EMPLOYEES);
        $this->deleteRole(self::ROLE_TEACHER);
        $this->deleteRole(self::ROLE_DEPARTMENT);
        $this->deleteRole(self::ROLE_STUDENT);
        $this->deleteRole(self::ROLE_PARENTS);
    }

    public function getPermissions()
    {
        return [
            'dashboard' => [
                'links' => [
                    '/admin/*',
                    '/admin/default/*',
                ],
                'viewDashboard' => [
                    'title' => 'Просмотр главной панели',
                    'roles' => [
                        self::ROLE_ADMIN
                    ],
                    'links' => [
                        '/admin',
                        '/admin/site/index',
                    ],
                ],
                'viewAdmintools' => [
                    'title' => 'Инструменты админа',
                    'roles' => [
                        self::ROLE_SYSTEM
                    ],
                    'links' => [
                        '/admin/admintools',
                    ],
                ],
                'viewDebug' => [
                    'title' => 'Доступ к Debug-панели',
                    'roles' => [
                        self::ROLE_DEV
                    ],
                    'links' => [
                        '/admin/debug',
                    ],
                ],
                'viewGii' => [
                    'title' => 'Доступ к Gii-панели',
                    'roles' => [
                        self::ROLE_DEV
                    ],
                    'links' => [
                        '/admin/gii',
                    ],
                ],
            ],
            'userCommonPermissions' => [
                'commonPermission' => [
                    'title' => 'Общий доступ',
                    'roles' => [
                        self::ROLE_ADMIN
                    ],
                ],
                'changeOwnPassword' => [
                    'title' => 'Изменение своего пароля',
                    'roles' => [
                        self::ROLE_USER,
                        self::ROLE_STUDENT,
                        self::ROLE_PARENTS,
                    ],
                ],
            ],
        ];
    }

}
