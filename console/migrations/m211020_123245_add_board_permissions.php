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
                    'title' => 'Просмотр объявления',
                    'links' => [
                        '/info/board/index',
                        '/info/board/view',
                        '/info/board/grid-sort',
                        '/info/board/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_AUTHOR,
                    ],
                ],
                'viewBoardAdmin' => [
                    'title' => 'Просмотр объявления(админ)',
                    'links' => [
                        '/admin/info/board/index',
                        '/admin/info/board/view',
                        '/admin/info/board/grid-sort',
                        '/admin/info/board/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_MODERATOR,
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
                    ],
                    'roles' => [
                        self::ROLE_AUTHOR,
                    ],
                    'childs' => [
                        'viewBoard',
                    ],
                ],
                'editBoardAdmin' => [
                    'title' => 'Редактирование объявлений(админ)',
                    'links' => [
                        '/admin/info/board/update',
                        '/admin/info/board/activate',
                        '/admin/info/board/deactivate',
                        '/admin/info/board/bulk-activate',
                        '/admin/info/board/bulk-deactivate',
                    ],
                    'roles' => [
                        self::ROLE_MODERATOR,
                    ],
                    'childs' => [
                        'viewBoardAdmin',
                    ],
                ],
                'createBoard' => [
                    'title' => 'Добавление объявлений',
                    'links' => [
                        '/info/board/create',
                    ],
                    'roles' => [
                        self::ROLE_AUTHOR,
                    ],
                    'childs' => [
                        'viewBoard',
                    ],
                ],
                'createBoardAdmin' => [
                    'title' => 'Добавление объявлений(админ)',
                    'links' => [
                        '/admin/info/board/create',
                    ],
                    'roles' => [
                        self::ROLE_MODERATOR,
                    ],
                    'childs' => [
                        'viewBoardAdmin',
                    ],
                ],
                'deleteBoard' => [
                    'title' => 'Удаление объявлений',
                    'links' => [
                        '/info/board/delete',
                        '/info/board/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_AUTHOR,
                    ],
                    'childs' => [
                        'createBoard',
                    ],
                ],
                'deleteBoardAdmin' => [
                    'title' => 'Удаление объявлений(админ)',
                    'links' => [
                        '/admin/info/board/delete',
                        '/admin/info/board/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_MODERATOR,
                    ],
                    'childs' => [
                        'viewBoardAdmin',
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
                        self::ROLE_MODERATOR,
                    ],
                ],
            ],
        ];
    }

}
