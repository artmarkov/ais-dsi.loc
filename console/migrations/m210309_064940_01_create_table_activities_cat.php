<?php

use yii\db\Migration;

class m210309_064940_01_create_table_activities_cat extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%activities_cat}}', [
            'id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128)->notNull(),
            'color' => $this->string(32),
            'rendering' => $this->tinyInteger(1)->notNull()->defaultValue('0')->comment('как фон или бар'),
            'description' => $this->string(256),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%activities_cat}}');
    }
}
