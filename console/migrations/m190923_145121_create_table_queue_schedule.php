<?php

use yii\db\Migration;

class m190923_145121_create_table_queue_schedule extends Migration
{
    public function up()
    {
         $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%queue_schedule}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(127)->notNull(),
            'class' => $this->string(127)->notNull(),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue('0'),
            'content' => $this->text(),
            'cron_expression' => $this->string(127)->notNull(),
            'priority' => $this->integer(),
            'run_now' => $this->tinyInteger(1)->notNull()->defaultValue('0'),
            'next_date' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('created_by', '{{%queue_schedule}}', 'created_by');
        $this->createIndex('updated_by', '{{%queue_schedule}}', 'updated_by');
        $this->createIndex('class', '{{%queue_schedule}}', 'class', true);
        $this->addForeignKey('queue_schedule_ibfk_1', '{{%queue_schedule}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('queue_schedule_ibfk_2', '{{%queue_schedule}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropTable('{{%queue_schedule}}');
    }
}
