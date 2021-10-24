<?php

use artsoft\db\PermissionsMigration;

class m190808_224834_add_fileinput_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('fileinputManagement', 'Менеджер загрузки');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('fileinputManagement');
    }

    public function getPermissions()
    {
        return [
            'fileinputManagement' => [
                'links' => [
                    '/fileinput/file-manager/*',
                    '/admin/fileinput/file-manager/*',
                ],
                'uploadFile' => [
                    'title' => 'Загрузка файлов',
                    'roles' => [self::ROLE_USER],
                    'links' => [
                        '/fileinput/file-manager/file-upload',
                        '/admin/fileinput/file-manager/file-upload',
                    ],
                ],   
                'sortFile' => [
                    'title' => 'Сортировка файлов',
                    'roles' => [self::ROLE_USER],
                    'links' => [
                        '/fileinput/file-manager/sort-file',
                        '/admin/fileinput/file-manager/sort-file',
                    ],
                    'childs' => [
                        'uploadFile',
                    ],
                ],   
                'deleteFile' => [
                    'title' => 'Удаление файлов',
                    'roles' => [self::ROLE_USER],
                    'links' => [
                        '/fileinput/file-manager/delete-file',
                        '/admin/fileinput/file-manager/delete-file',
                    ],
                    'childs' => [
                        'uploadFile',
                        'sortFile',
                    ],
                ],   
                'fullFileinputAccess' => [
                    'title' => 'Полный доступ к файлам',
                    'roles' => [self::ROLE_ADMIN],
                ],
            ],
        ];
    }

}
