<?php

use yii\db\Migration;

class m210301_151054_007_create_table_division extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%division}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(127)->notNull(),
            'slug' => $this->string(32)->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%division}}');
    }
}
