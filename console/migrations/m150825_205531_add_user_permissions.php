<?php

use artsoft\db\PermissionsMigration;

class m150825_205531_add_user_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('userManagement', 'Управление учетными записями');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('userManagement');
    }

    public function getPermissions()
    {
        return [
            'userManagement' => [
                'links' => [
                    '/admin/user/*',
                    '/admin/user/default/*',
                    '/admin/user/role/*',
                    '/admin/user/permission/*',
                    '/admin/user/permission-groups/*',
                    '/admin/user/user-permission/*',
                ],
                'viewUsers' => [
                    'title' => 'Просмотр учетной записи',
                    'roles' => [self::ROLE_ADMIN],
                    'links' => [
                        '/admin/user/default/index',
                        '/admin/user/default/grid-sort',
                        '/admin/user/default/grid-page-size',
                    ],
                ],
                'editUsers' => [
                    'title' => 'Редактирование учетной записи',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewUsers'],
                    'links' => [
                        '/admin/user/default/update',
                        '/admin/user/default/bulk-activate',
                        '/admin/user/default/bulk-deactivate',
                        '/admin/user/default/toggle-attribute',
                    ],
                ],
                'impersonateUsers' => [
                    'title' => 'Вход под пользователем',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewUsers'],
                    'links' => [
                        '/admin/user/default/impersonate',
                    ],
                ],
                'sendLoginUsers' => [
                    'title' => 'Отправлять регистрационные данные',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewUsers'],
                    'links' => [
                        '/admin/user/default/send-login',
                    ],
                ],
                'createUsers' => [
                    'title' => 'Добавление учетной записи',
                    'roles' => [self::ROLE_ADMIN],
                    'childs' => ['viewUsers'],
                    'links' => [
                        '/admin/user/default/create',
                    ],
                ],
                'deleteUsers' => [
                    'title' => 'Удаление учетной записи',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewUsers'],
                    'links' => [
                        '/admin/user/default/delete',
                        '/admin/user/default/bulk-delete',
                    ],
                ],
                'changeUserPassword' => [
                    'title' => 'Изменение пароля учетных записей',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewUsers'],
                    'links' => [
                        '/admin/user/default/change-password',
                    ],
                ],
                'viewRolesAndPermissions' => [
                    'title' => 'Просмотр Ролей И Прав',
                    'roles' => [self::ROLE_ADMIN],
                    'childs' => ['viewUsers', 'viewUserRoles'],
                    'links' => [
                        '/admin/user/permission-groups/index',
                        '/admin/user/permission-groups/grid-sort',
                        '/admin/user/permission-groups/grid-page-size',
                        '/admin/user/permission/index',
                        '/admin/user/permission/grid-sort',
                        '/admin/user/permission/grid-page-size',
                        '/admin/user/role/index',
                        '/admin/user/role/grid-sort',
                        '/admin/user/role/grid-page-size',
                    ],
                ],
                'manageRolesAndPermissions' => [
                    'title' => 'Управление Ролями И Правами',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewRolesAndPermissions', 'viewUsers', 'editUsers'],
                    'links' => [
                        '/admin/user/permission-groups/update',
                        '/admin/user/permission-groups/create',
                        '/admin/user/permission-groups/delete',
                        '/admin/user/permission-groups/bulk-delete',
                        '/admin/user/permission/update',
                        '/admin/user/permission/create',
                        '/admin/user/permission/delete',
                        '/admin/user/permission/bulk-delete',
                        '/admin/user/permission/view',
                        '/admin/user/permission/refresh-routes',
                        '/admin/user/permission/set-child-permissions',
                        '/admin/user/permission/set-child-routes',
                        '/admin/user/role/update',
                        '/admin/user/role/create',
                        '/admin/user/role/delete',
                        '/admin/user/role/bulk-delete',
                        '/admin/user/role/view',
                        '/admin/user/role/set-child-permissions',
                        '/admin/user/role/set-child-roles',
                    ],
                ],
                'assignRolesToUsers' => [
                    'title' => 'Назначение Ролей Пользователям',
                    'roles' => [self::ROLE_SYSTEM],
                    'childs' => ['viewUsers', 'viewUserRoles'],
                    'links' => [
                        '/admin/user/user-permission/set',
                        '/admin/user/user-permission/set-roles',
                    ],
                ],
                'bindUserToIp' => [
                    'title' => 'Привязка Пользователя К IP',
                    'roles' => [self::ROLE_ADMIN],
                ],
                'editUserEmail' => [
                    'title' => 'Редактирование Email Пользователя',
                    'roles' => [self::ROLE_ADMIN],
                    'childs' => ['viewUserEmail'],
                ],
                'editUserSnils' => [
                    'title' => 'Редактирование СНИЛС Пользователя',
                    'roles' => [self::ROLE_ADMIN],
                    'childs' => ['viewUserSnils'],
                ],
                'viewRegistrationIp' => [
                    'title' => 'Просмотр Регистрационного IP-адреса',
                    'roles' => [self::ROLE_SYSTEM],
                ],
                'viewUserEmail' => [
                    'title' => 'Просмотр Email Пользователя',
                    'roles' => [self::ROLE_ADMIN],
                ],
                'viewUserSnils' => [
                    'title' => 'Просмотр СНИЛС Пользователя',
                    'roles' => [self::ROLE_ADMIN],
                ],
                'viewUserRoles' => [
                    'title' => 'Просмотр Ролей Пользователя',
                    'roles' => [self::ROLE_ADMIN],
                ],
            ],
        ];
    }

}
