<?php

use yii\db\Migration;

class m210301_151057_013_create_table_subject extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%subject}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'name' => $this->string(64),
            'slug' => $this->string(32),
            'order' => $this->integer(8)->unsigned()->notNull(),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%subject}}');
    }
}
