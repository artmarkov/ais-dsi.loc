<?php

use yii\db\Migration;

class m190923_145121_create_table_queue_schedule extends Migration
{
    public function up()
    {
        $tableOptions = null;

        $this->createTable('queue', [
            'id' => $this->bigPrimaryKey(),
            'channel' => $this->string(255)->notNull(),
            'job' => $this->binary()->notNull(),
            'pushed_at' => $this->integer(11)->notNull(),
            'ttr' => $this->integer(11)->notNull(),
            'delay' => $this->integer(11)->notNull()->defaultValue('0'),
            'priority' => $this->integer(11)->unsigned()->notNull()->defaultValue('1024'),
            'reserved_at' => $this->integer(11)->defaultValue(null),
            'attempt' => $this->integer(11)->defaultValue(null),
            'done_at' => $this->integer(11)->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('channel', 'queue', 'channel');
        $this->createIndex('reserved_at', 'queue', 'reserved_at');
        $this->createIndex('priority', 'queue', 'priority');

        $this->createTable('queue_schedule', [
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

        $this->addCommentOnTable('queue_schedule', 'Назначенные задания');

//        $this->createIndex('created_by', 'queue_schedule', 'created_by');
        $this->createIndex('updated_by', 'queue_schedule', 'updated_by');
        $this->createIndex('class', 'queue_schedule', 'class', true);
        $this->addForeignKey('queue_schedule_ibfk_1', 'queue_schedule', 'created_by', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('queue_schedule_ibfk_2', 'queue_schedule', 'updated_by', 'users', 'id', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropForeignKey('queue_schedule_ibfk_1', 'queue_schedule');
        $this->dropForeignKey('queue_schedule_ibfk_2', 'queue_schedule');
        $this->dropTable('queue_schedule');
        $this->dropTable('queue');
    }
}
