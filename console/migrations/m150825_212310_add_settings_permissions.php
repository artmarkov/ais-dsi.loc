<?php

use artsoft\db\PermissionsMigration;

class m150825_212310_add_settings_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('settings', 'Settings');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('settings');
    }

    public function getPermissions()
    {
        return [
            'settings' => [
                'links' => [
                    '/admin/settings/*',
                    '/admin/settings/default/*',
                ],
                'changeGeneralSettings' => [
                    'title' => 'Изменение Общих настроек',
                    'links' => ['/admin/settings/default/index'],
                    'roles' => [self::ROLE_ADMIN],
                ],
                'changeReadingSettings' => [
                    'title' => 'Изменение Настроек Форм',
                    'links' => ['/admin/settings/reading/index'],
                    'roles' => [self::ROLE_ADMIN],
                ],
                'changeOwnSettings' => [
                    'title' => 'Изменение Сведений об Организации',
                    'links' => ['/admin/settings/own/index'],
                    'roles' => [self::ROLE_MODERATOR],
                ],
                'flushCache' => [
                    'title' => 'Очистка Кэша',
                    'links' => ['/admin/settings/cache/flush'],
                    'roles' => [self::ROLE_ADMIN],
                ],
            ],
        ];
    }

}
