<?php

use yii\db\Migration;

class m210301_151051_001_create_table_auditory_building extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%auditory_building}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'name' => $this->string(128)->notNull(),
            'slug' => $this->string(64)->notNull(),
            'address' => $this->string()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%auditory_building}}');
    }
}
