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
                    'title' => 'Change General Settings',
                    'links' => ['/admin/settings/default/index'],
                    'roles' => [self::ROLE_ADMIN],
                ],
                'changeReadingSettings' => [
                    'title' => 'Change Reading Settings',
                    'links' => ['/admin/settings/reading/index'],
                    'roles' => [self::ROLE_ADMIN],
                ],
                'flushCache' => [
                    'title' => 'Flush Cache',
                    'links' => ['/admin/settings/cache/flush'],
                    'roles' => [self::ROLE_ADMIN],
                ],
            ],
        ];
    }

}
