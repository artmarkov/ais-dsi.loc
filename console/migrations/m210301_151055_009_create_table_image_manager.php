<?php

use yii\db\Migration;

class m210301_151055_009_create_table_image_manager extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%image_manager}}', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(256)->notNull(),
            'class' => $this->string(256),
            'item_id' => $this->integer(8),
            'alt' => $this->string(256),
            'sort' => $this->smallInteger(5)->notNull(),
            'type' => $this->string(32)->notNull(),
            'filetype' => $this->string(32),
            'url' => $this->string(256),
            'size' => $this->integer()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%image_manager}}');
    }
}
