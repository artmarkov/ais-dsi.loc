<?php

use \artsoft\db\BaseMigration;

class m130524_201442_init extends BaseMigration
{
    public function up()
    {
        $tableOptions = null;

        $this->createTableWithHistory('users', [
            'id' => $this->primaryKey().' constraint check_range check (id between 1000 and 9999)',
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'superadmin'=> $this->integer(6)->defaultValue(0),
            'registration_ip' => $this->string(15),
            'bind_to_ip' => $this->string(255),
            'email_confirmed' => $this->integer(1)->defaultValue(0),
            'confirmation_token' => $this->string(255),
            'avatar' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'user_category' => $this->smallInteger()->notNull()->defaultValue(1),
            'first_name' => $this->string(124),
            'last_name' => $this->string(124),
            'middle_name' => $this->string(124),
            'birth_timestamp' => $this->integer()->notNull(),
            'gender' => $this->integer(1),
            'phone' => $this->string(24),
            'phone_optional' => $this->string(24),
            'skype' => $this->string(64),
            'info' => $this->string(255),
            'snils' => $this->string(16),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('users','Пользователи');
        $this->db->createCommand()->resetSequence('users',1000)->execute();
    }

    public function down()
    {
        $this->dropTableWithHistory('users');
    }
}
