<?php

use yii\db\Migration;

class m210301_151057_014_create_table_subject_category_item extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%subject_category_item}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(127),
            'slug' => $this->string(64)->notNull(),
            'order' => $this->tinyInteger(2)->unsigned()->notNull(),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%subject_category_item}}');
    }
}
