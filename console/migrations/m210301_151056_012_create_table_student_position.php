<?php

use yii\db\Migration;

class m210301_151056_012_create_table_student_position extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%student_position}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
            'status' => $this->smallInteger(1)->unsigned()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%student_position}}');
    }
}
