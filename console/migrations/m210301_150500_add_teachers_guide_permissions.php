<?php

use artsoft\db\PermissionsMigration;

class m210301_150500_add_teachers_guide_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('guidejobManagement', 'Справочник "Кадры"');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('guidejobManagement');
    }

    public function getPermissions()
    {
        return [
            'guidejobManagement' => [
                'links' => [
                    '/admin/guidejob/*',
                    '/admin/guidejob/default/*',
                    '/admin/guidejob/direction-vid/*',
                    '/admin/guidejob/direction/*',
                    '/admin/guidejob/level/*',
                    '/admin/guidejob/position/*',
                    '/admin/guidejob/stake/*',
                    '/admin/guidejob/cost/*',
                    '/admin/guidejob/bonus/*',
                    '/admin/guidejob/bonus-category/*',
                ],
                'viewGuidejob' => [
                    'title' => 'Просмотр Справочника "Кадры"',
                    'links' => [
                        '/admin/guidejob/default/index',
                        '/admin/guidejob/default/view',
                        '/admin/guidejob/default/grid-page-size',
                        '/admin/guidejob/direction-vid/index',
                        '/admin/guidejob/direction-vid/view',
                        '/admin/guidejob/direction-vid/grid-page-size',
                        '/admin/guidejob/direction/index',
                        '/admin/guidejob/direction/view',
                        '/admin/guidejob/direction/grid-page-size',
                        '/admin/guidejob/level/index',
                        '/admin/guidejob/level/view',
                        '/admin/guidejob/level/grid-page-size',
                        '/admin/guidejob/position/index',
                        '/admin/guidejob/position/view',
                        '/admin/guidejob/position/grid-page-size',
                        '/admin/guidejob/stake/index',
                        '/admin/guidejob/stake/view',
                        '/admin/guidejob/stake/grid-page-size',
                        '/admin/guidejob/cost/index',
                        '/admin/guidejob/cost/view',
                        '/admin/guidejob/cost/grid-page-size',
                        '/admin/guidejob/bonus/index',
                        '/admin/guidejob/bonus/view',
                        '/admin/guidejob/bonus/grid-page-size',
                        '/admin/guidejob/bonus-category/index',
                        '/admin/guidejob/bonus-category/view',
                        '/admin/guidejob/bonus-category/grid-page-size',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                ],
                'editGuidejob' => [
                    'title' => 'Редактирование записи "Кадры"',
                    'links' => [
                        '/admin/guidejob/default/update',
                        '/admin/guidejob/direction-vid/update',
                        '/admin/guidejob/direction/update',
                        '/admin/guidejob/level/update',
                        '/admin/guidejob/position/update',
                        '/admin/guidejob/stake/update',
                        '/admin/guidejob/cost/update',
                        '/admin/guidejob/bonus/update',
                        '/admin/guidejob/bonus-category/update',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewGuidejob',
                    ],
                ],
                'createGuidejob' => [
                    'title' => 'Добавление записи в "Кадры"',
                    'links' => [
                        '/admin/guidejob/default/create',
                        '/admin/guidejob/direction-vid/create',
                        '/admin/guidejob/direction/create',
                        '/admin/guidejob/level/create',
                        '/admin/guidejob/position/create',
                        '/admin/guidejob/stake/create',
                        '/admin/guidejob/cost/create',
                        '/admin/guidejob/bonus/create',
                        '/admin/guidejob/bonus-category/create',

                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'viewGuidejob',
                    ],
                ],
                'deleteGuidejob' => [
                    'title' => 'Удаление записи из "Кадры"',
                    'links' => [
                        '/admin/guidejob/default/delete',
                        '/admin/guidejob/default/bulk-delete',
                        '/admin/guidejob/direction-vid/delete',
                        '/admin/guidejob/direction-vid/bulk-delete',
                        '/admin/guidejob/direction/delete',
                        '/admin/guidejob/direction/bulk-delete',
                        '/admin/guidejob/level/delete',
                        '/admin/guidejob/level/bulk-delete',
                        '/admin/guidejob/position/delete',
                        '/admin/guidejob/position/bulk-delete',
                        '/admin/guidejob/stake/delete',
                        '/admin/guidejob/stake/bulk-delete',
                        '/admin/guidejob/cost/delete',
                        '/admin/guidejob/cost/bulk-delete',
                        '/admin/guidejob/bonus/delete',
                        '/admin/guidejob/bonus/bulk-delete',
                        '/admin/guidejob/bonus-category/delete',
                        '/admin/guidejob/bonus-category/bulk-delete',
                    ],
                    'roles' => [
                        self::ROLE_SYSTEM,
                    ],
                    'childs' => [
                        'createGuidejob',
                    ],
                ],
            ],
        ];
    }

}
