<?php

use artsoft\db\PermissionsMigration;

class m191016_173145_add_queue_schedule_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('queueManagement', 'Управление очередями');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('queueManagement');
    }

    public function getPermissions()
    {
        return [
            'queueManagement' => [
                'links' => [
                    '/admin/queue-schedule/*',
                    '/admin/queue-schedule/default/*',
                    '/admin/queue-schedule/queue-layers/*',
                ],
                'viewQueue' => [
                    'title' => 'Просмотр задания',
                    'links' => [
                        '/admin/queue-schedule/default/index',
                        '/admin/queue-schedule/default/view',
                        '/admin/queue-schedule/default/grid-sort',
                        '/admin/queue-schedule/default/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                ],
                'editQueue' => [
                    'title' => 'Редактирование заданий',
                    'links' => [
                        '/admin/queue-schedule/default/update',
                        '/admin/queue-schedule/default/run',
                        '/admin/queue-schedule/default/activate',
                        '/admin/queue-schedule/default/deactivate',
                        '/admin/queue-schedule/default/bulk-run',
                        '/admin/queue-schedule/default/bulk-activate',
                        '/admin/queue-schedule/default/bulk-deactivate',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewQueue',
                    ],
                ],
                'createQueue' => [
                    'title' => 'Добавление заданий',
                    'links' => [
                        '/admin/queue-schedule/default/create',
                        
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewQueue',
                    ],
                ],
                'deleteQueue' => [
                    'title' => 'Удаление заданий',
                    'links' => [
                        '/admin/queue-schedule/default/delete',
                        '/admin/queue-schedule/default/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'createQueue',
                    ],
                ],
                'editClassJob' => [
                    'title' => 'Редактировать класс задания',
                    'roles' => [
                        self::ROLE_SYSTEM
                    ],
                ],
            ],
        ];
    }

}
