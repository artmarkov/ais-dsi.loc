<?php

use yii\db\Migration;

class m170529_050554_create_table_system extends Migration
{
    public function up()
    {
        $this->createTable('mail_queue', [
            'id'               => $this->primaryKey(),
            'created_at'       => $this->dateTime()->notNull(),
            'sent_at'          => $this->dateTime(),
            'created_by'       => $this->integer()->notNull(),
            'rcpt_to'          => $this->string(4000),
            'subject'          => $this->string(500),
            'message'          => $this->text(),
            'content_type'     => $this->string(30)->notNull(),
            'file_name'        => $this->string(500),
            'file_type'        => $this->string(50),
            'file_data'        => $this->binary()
        ]);
        $this->addCommentOnTable('mail_queue','Журнал отправленных email-сообщений');

        $this->createTable('files', [
            'id'               => $this->primaryKey(),
            'name'             => $this->string(500)->notNull(),
            'size'             => $this->bigInteger()->notNull(),
            'content'          => $this->binary(),
            'type'             => $this->string(100)->notNull()->defaultValue('application/octet-stream'),
            'created_at'       => $this->integer()->notNull(),
            'created_by'       => $this->integer()->notNull(),
            'deleted_at'       => $this->integer(),
            'deleted_by'       => $this->integer(),
            'object_type'      => $this->string(50),
            'object_id'        => $this->integer()
        ]);
        $this->addCommentOnTable('mail_queue','Таблица файлов');

        $this->createTable('session', [
            'id' => $this->char(64)->notNull(),
            'expire' => $this->integer(),
            'data' => $this->text()
        ]);
        $this->addPrimaryKey('session_pk', 'session', 'id');;
        $this->addCommentOnTable('session','Сессии');

        $this->createTable('requests', [
            'id'               => $this->primaryKey(),
            'created_at'       => $this->dateTime(),
            'user_id'          => $this->integer()->notNull(),
            'url'              => $this->string(2000)->notNull(),
            'post'             => $this->text(),
            'time'             => $this->decimal(10,2),
            'mem_usage_mb'     => $this->decimal(6,2),
            'http_status'      => $this->integer()

        ]);
        $this->addCommentOnTable('requests','Запросы');
        $this->addForeignKey('requests_ibfk_user', 'requests', ['user_id'], 'users', ['id'], 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropTable('requests');
        $this->dropTable('session');
        $this->dropTable('files');
        $this->dropTable('mail_queue');
    }
}