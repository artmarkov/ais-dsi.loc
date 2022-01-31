<?php


class m220116_191645_ref extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

    }

    public function down()
    {

    }
}
