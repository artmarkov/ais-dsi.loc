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
                ],
                'viewBoard' => [
                    'title' => 'Просмотр объявлений',
                    'links' => [
                        '/info/board/index',
                        '/info/board/view',
                        '/info/board/grid-sort',
                        '/info/board/grid-page-size',
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
            ],
        ];
    }

}
