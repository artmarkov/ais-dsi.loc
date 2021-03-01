<?php

use yii\db\Migration;

class m210301_151052_002_create_table_auditory_cat extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%auditory_cat}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'name' => $this->string(128)->notNull(),
            'description' => $this->string(256)->notNull(),
            'study_flag' => $this->tinyInteger(1)->notNull()->defaultValue('0'),
            'order' => $this->integer(8)->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%auditory_cat}}');
    }
}
