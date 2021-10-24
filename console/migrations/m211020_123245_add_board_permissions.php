<?php

use artsoft\db\PermissionsMigration;

class m211020_123245_add_board_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('boardManagement', 'Управление объявлениями');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('boardManagement');
    }

    public function getPermissions()
    {
        return [
            'boardManagement' => [
                'links' => [
                    '/info/board/*',
                    '/admin/info/board/*',
                ],
                'viewBoard' => [
                    'title' => 'Просмотр объявлений',
                    'links' => [
                        '/info/board/index',
                        '/info/board/view',
                        '/info/board/grid-sort',
                        '/info/board/grid-page-size',
                        '/admin/info/board/index',
                        '/admin/info/board/view',
                        '/admin/info/board/grid-sort',
                        '/admin/info/board/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_USER,
                    ],
                ],
                'editBoard' => [
                    'title' => 'Редактирование объявлений',
                    'links' => [
                        '/info/board/update',
                        '/info/board/activate',
                        '/info/board/deactivate',
                        '/info/board/bulk-activate',
                        '/info/board/bulk-deactivate',
                        '/admin/info/board/update',
                        '/admin/info/board/activate',
                        '/admin/info/board/deactivate',
                        '/admin/info/board/bulk-activate',
                        '/admin/info/board/bulk-deactivate',
                    ],
                    'roles' => [
                        self::ROLE_USER,
                    ],
                    'childs' => [
                        'viewBoard',
                    ],
                ],
                'createBoard' => [
                    'title' => 'Добавление объявлений',
                    'links' => [
                        '/info/board/create',
                        '/admin/info/board/create',
                    ],
                    'roles' => [
                        self::ROLE_USER,
                    ],
                    'childs' => [
                        'viewBoard',
                    ],
                ],
                'deleteBoard' => [
                    'title' => 'Удаление объявлений',
                    'links' => [
                        '/info/board/delete',
                        '/info/board/bulk-delete',
                        '/admin/info/board/delete',
                        '/admin/info/board/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_USER,
                    ],
                    'childs' => [
                        'createBoard',
                    ],
                ],
                'editBoardAuthor' => [
                    'title' => 'Редактировать автора объявления',
                    'roles' => [
                        self::ROLE_ADMIN
                    ],
                ],
                'fullBoardAccess' => [
                    'title' => 'Полный доступ к объявлениям',
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'boardCatAllAccess' => [
                    'title' => 'Отправлять всем пользователям',
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'boardCatStudentAccess' => [
                    'title' => 'Отправлять всем ученикам',
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'boardCatEmployeesAccess' => [
                    'title' => 'Отправлять всем сотрудникам',
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'boardCatTeachersAccess' => [
                    'title' => 'Отправлять всем преподавателям',
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'boardCatParentsAccess' => [
                    'title' => 'Отправлять всем родителям',
                    'roles' => [
                        self::ROLE_ADMIN,
                    ],
                ],
                'boardCatSelectAccess' => [
                    'title' => 'Право выбирать из списка',
                    'roles' => [
                        self::ROLE_ADMIN,
                        self::ROLE_USER,
                    ],
                ],
            ],
        ];
    }

}
