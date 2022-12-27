<?php

use \artsoft\db\BaseMigration;

class m221125_201442_option extends BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['stake_name', 'guide_teachers_stake', 'id', 'name', 'name', 'status', null, 'Названия ставок преподавателей'],
        ])->execute();
    }

        public function down()
    {

    }
}
