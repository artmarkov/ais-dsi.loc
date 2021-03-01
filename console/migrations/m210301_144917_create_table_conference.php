<?php

use yii\db\Migration;

class m210301_144917_create_table_conference extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%conference}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(127)->notNull(),
            'start_date' => $this->integer()->notNull(),
            'end_date' => $this->integer()->notNull(),
        ], $tableOptions);

    }


    public function down()
    {
        $this->dropTable('{{%conference}}');
    }
}
