<?php

use yii\db\Migration;

class m210301_151056_create_table_measure extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%measure_unit}}', [
            'id' => $this->smallInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(16),
            'slug' => $this->string(8),
        ], $tableOptions);

        $this->createTable('{{%measure}}', [
            'id' => $this->smallInteger()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'category_id' => $this->integer(8)->notNull(),
            'name' => $this->string(64),
            'abbr' => $this->string(32),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%measure}}');
        $this->dropTable('{{%measure_unit}}');
    }
}
