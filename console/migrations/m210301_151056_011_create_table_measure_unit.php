<?php

use yii\db\Migration;

class m210301_151056_011_create_table_measure_unit extends Migration
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

    }

    public function down()
    {
        $this->dropTable('{{%measure_unit}}');
    }
}
