<?php

use artsoft\db\PermissionsMigration;

class m150821_140141_add_core_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('dashboard', 'Главная панель');
        $this->addPermissionsGroup('userCommonPermissions', 'Общий доступ');

        $this->addRole(self::ROLE_ADMIN, 'Администратор');
        $this->addRole(self::ROLE_MODERATOR, 'Модератор');
        $this->addRole(self::ROLE_AUTHOR, 'Автор');
        $this->addRole(self::ROLE_USER, 'Пользователь');
        $this->addRole(self::ROLE_EMPLOYEES, 'Сотрудник');
        $this->addRole(self::ROLE_TEACHER, 'Преподаватель');
        $this->addRole(self::ROLE_STUDENT, 'Ученик');
        $this->addRole(self::ROLE_CURATOR, 'Родитель');

        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'user', 'child' => 'teacher']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'user', 'child' => 'student']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'user', 'child' => 'curator']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'author', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'moderator', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'moderator', 'child' => 'employees']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'moderator', 'child' => 'author']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'author']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'moderator']);
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('dashboard');
        $this->deletePermissionsGroup('userCommonPermissions');

        $this->deleteRole(self::ROLE_ADMIN);
        $this->deleteRole(self::ROLE_MODERATOR);
        $this->deleteRole(self::ROLE_AUTHOR);
        $this->deleteRole(self::ROLE_USER);
        $this->deleteRole(self::ROLE_EMPLOYEES);
        $this->deleteRole(self::ROLE_TEACHER);
        $this->deleteRole(self::ROLE_STUDENT);
        $this->deleteRole(self::ROLE_CURATOR);
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
                    'roles' => [self::ROLE_MODERATOR],
                    'links' => [
                        '/admin',
                        '/admin/site/index',
                    ],
                ],
            ],
            'userCommonPermissions' => [
                'commonPermission' => [
                    'title' => 'Общий доступ',
                    'roles' => [self::ROLE_MODERATOR],
                ],
                'changeOwnPassword' => [
                    'title' => 'Изменение своего пароля',
                    'roles' => [self::ROLE_USER],
                ],
            ],
        ];
    }

}
