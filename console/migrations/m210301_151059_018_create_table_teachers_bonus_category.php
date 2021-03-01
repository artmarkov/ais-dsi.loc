<?php

use yii\db\Migration;

class m210301_151059_018_create_table_teachers_bonus_category extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%teachers_bonus_category}}', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(128)->notNull(),
            'slug' => $this->string(127)->notNull(),
            'multiple' => $this->tinyInteger(1)->notNull()->defaultValue('0'),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%teachers_bonus_category}}');
    }
}
