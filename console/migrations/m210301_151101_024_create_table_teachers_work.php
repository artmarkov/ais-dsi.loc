<?php

use yii\db\Migration;

class m210301_151101_024_create_table_teachers_work extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%teachers_work}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%teachers_work}}');
    }
}
