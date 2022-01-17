<?php


class m220116_191645_ref extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['education_programm_short_name', 'education_programm', 'id', 'short_name', 'id', 'status', null, 'Образовательные программы сокр.'],
        ])->execute();
    }

    public function down()
    {

    }
}
