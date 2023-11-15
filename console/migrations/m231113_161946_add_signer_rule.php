<?php

use artsoft\db\PermissionsMigration;

class m231113_161946_add_signer_rule extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('signerManagement', 'Права подписи');
        $this->addRole('signerSchedule', 'Подписант расписания преподавателей');
        $this->addRole('signerScheduleConsult', 'Подписант расписания консультаций');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('signerManagement');
        $this->deleteRole('signerSchedule');
        $this->deleteRole('signerScheduleConsult');
    }

    public function getPermissions()
    {
        return [];
    }

}
