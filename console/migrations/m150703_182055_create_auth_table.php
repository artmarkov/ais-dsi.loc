<?php

use yii\db\Migration;
use yii\db\Schema;

class m150703_182055_create_auth_table extends Migration
{

    const TABLE_NAME = 'auth';
    
    public function safeUp()
    {
        $tableOptions = null;

        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => $this->string(255)->notNull(),
            'source_id' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_auth_user', self::TABLE_NAME, 'user_id', 'users', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}