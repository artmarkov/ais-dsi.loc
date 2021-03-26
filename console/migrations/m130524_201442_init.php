<?php

use \artsoft\db\BaseMigration;

class m130524_201442_init extends BaseMigration
{
    public function up()
    {
        $tableOptions = null;

        $this->createTable('users', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'email_confirmed' => $this->integer(1)->defaultValue(0),
            'superadmin' => $this->integer(6)->defaultValue(0),
            'registration_ip' => $this->string(15),
            'bind_to_ip' => $this->string(255),
            'confirmation_token' => $this->string(255),
            'avatar' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('users', 'Учетные записи');
        $this->db->createCommand()->resetSequence('users', 1000)->execute();

        $this->createTableWithHistory('common_users', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'user_category' => $this->smallInteger()->notNull()->defaultValue(1),
            'first_name' => $this->string(124),
            'last_name' => $this->string(124),
            'middle_name' => $this->string(124),
            'birth_timestamp' => $this->integer(),
            'gender' => $this->integer(1),
            'phone' => $this->string(24),
            'phone_optional' => $this->string(24),
            'skype' => $this->string(64),
            'info' => $this->text(),
            'snils' => $this->string(16),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('common_users', 'Общие данные');
        $this->db->createCommand()->resetSequence('common_users', 1000)->execute();
    }

    public function down()
    {
        $this->dropTable('common_users');
        $this->dropTableWithHistory('users');
    }
}
