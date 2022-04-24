<?php

use artsoft\db\PermissionsMigration;

class m190718_115610_add_mailbox_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('mailboxManagement', 'Управление почтой');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('mailboxManagement');
    }

    public function getPermissions()
    {
        return [
            'mailboxManagement' => [
                'links' => [
                    '/mailbox/default/*',
                ],
                'viewMail' => [
                    'title' => 'Просмотр писем',
                    'roles' => [self::ROLE_USER],
                    'links' => [
                        '/mailbox/default/index',
                        '/mailbox/default/index-sent',
                        '/mailbox/default/index-draft',
                        '/mailbox/default/index-trash',
                        '/mailbox/default/view-inbox',
                        '/mailbox/default/view-sent',
                        '/mailbox/default/grid-page-size',
                    ],
                ],
                'composeMail' => [
                    'title' => 'Отправка писем',
                    'roles' => [self::ROLE_USER],
                    'childs' => ['viewMail'],
                    'links' => [
                        '/mailbox/default/compose',
                        '/mailbox/default/update',
                        '/mailbox/default/delete',
                        '/mailbox/default/reply',
                        '/mailbox/default/forward',
                        '/mailbox/default/trash',
                        '/mailbox/default/trash-sent',
                        '/mailbox/default/restore',
                        '/mailbox/default/bulk-mark-read',
                        '/mailbox/default/bulk-mark-unread',
                        '/mailbox/default/bulk-trash',
                        '/mailbox/default/bulk-trash-sent',
                        '/mailbox/default/bulk-delete',
                        '/mailbox/default/bulk-restore',
                        '/mailbox/default/clian-own',
                    ],
                ],
                'cliarTrashMail' => [
                    'title' => 'Очистка корзины',
                    'roles' => [self::ROLE_ADMIN],
                    'childs' => ['viewMail', 'composeMail'],
                    'links' => [
                        '/mailbox/default/clian',
                    ],
                ],                
            ],
        ];
    }

}
