<?php

use yii\db\Migration;

class m210301_151058_015_create_table_subject_type extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%subject_type}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(127)->notNull(),
            'slug' => $this->string(64)->notNull(),
            'status' => $this->tinyInteger(1)->unsigned()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%subject_type}}');
    }
}
