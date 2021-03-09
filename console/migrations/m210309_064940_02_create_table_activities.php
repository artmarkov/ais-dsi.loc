<?php

use yii\db\Migration;

class m210309_064940_02_create_table_activities extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%activities}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->smallInteger(3)->unsigned()->notNull(),
            'auditory_id' => $this->integer(8)->unsigned()->notNull(),
            'title' => $this->string(100),
            'description' => $this->text(),
            'start_timestamp' => $this->integer()->notNull(),
            'end_timestamp' => $this->integer(),
            'all_day' => $this->tinyInteger(1)->defaultValue('0'),
        ], $tableOptions);

        $this->createIndex('auditory_id', '{{%activities}}', 'auditory_id');
        $this->createIndex('category_id', '{{%activities}}', 'category_id');
        $this->addForeignKey('activities_ibfk_1', '{{%activities}}', 'category_id', '{{%activities_cat}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%activities}}');
    }
}
