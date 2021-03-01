<?php

use yii\db\Migration;

class m210301_151058_017_create_table_teachers_bonus extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%teachers_bonus}}', [
            'id' => $this->primaryKey(8),
            'teachers_id' => $this->integer(8),
            'bonus_item_id' => $this->integer(8)->notNull(),
        ], $tableOptions);

        $this->createIndex('bonus_item_id', '{{%teachers_bonus}}', 'bonus_item_id');
        $this->createIndex('teachers_id', '{{%teachers_bonus}}', 'teachers_id');
    }

    public function down()
    {
        $this->dropTable('{{%teachers_bonus}}');
    }
}
