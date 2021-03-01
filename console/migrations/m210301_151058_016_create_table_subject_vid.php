<?php

use yii\db\Migration;

class m210301_151058_016_create_table_subject_vid extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%subject_vid}}', [
            'id' => $this->tinyInteger(2)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(64)->notNull(),
            'slug' => $this->string(32)->notNull(),
            'qty_min' => $this->smallInteger(3)->unsigned()->notNull(),
            'qty_max' => $this->smallInteger(3)->unsigned()->notNull(),
            'info' => $this->text()->notNull(),
            'status' => $this->tinyInteger(1)->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%subject_vid}}');
    }
}
