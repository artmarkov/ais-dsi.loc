<?php

use artsoft\db\PermissionsMigration;

class m231113_161946_add_signer_rule extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('signerManagement', 'Права подписи');
        $this->addRole('signerSchedule', 'Подписант расписания преподавателей');
        $this->addRole('signerScheduleConsult', 'Подписант расписания консультаций');
        $this->addRole('signerSchoolplan', 'Подписант плана работы школы');
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('signerManagement');
        $this->deleteRole('signerSchoolplan');
        $this->deleteRole('signerScheduleConsult');
        $this->deleteRole('signerSchedule');
    }

    public function getPermissions()
    {
        return [];
    }

}
