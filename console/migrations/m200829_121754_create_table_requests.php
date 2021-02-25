<?php

use yii\db\Migration;

class m200829_121754_create_table_requests extends Migration
{
    public function up()
    {
        $this->createTable('{{%requests}}', [
            'id'               => $this->primaryKey(),
            'created_at'       => $this->dateTime(),
            'user_id'          => $this->integer()->notNull(),
            'url'              => $this->string(2000)->notNull(),
            'post'             => $this->text(),
            'time'             => $this->decimal(10,2),
            'mem_usage_mb'     => $this->decimal(6,2),
            'http_status'      => $this->integer()

        ]);
        $this->addForeignKey('requests_ibfk_user', '{{%requests}}', ['user_id'], '{{%user}}', ['id'], 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropTable('{{%requests}}');
    }
}