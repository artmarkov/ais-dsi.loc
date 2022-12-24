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
            'password_hash' => $this->string(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string(255),
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
        $this->db->createCommand()->resetSequence('users', 1100)->execute();

        $this->createTableWithHistory('user_common', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'user_id' => $this->integer(),
            'user_category' => $this->string(124)->notNull()->defaultValue(\common\models\user\UserCommon::USER_CATEGORY_EMPLOYEES),
            'last_name' => $this->string(124),
            'first_name' => $this->string(124),
            'middle_name' => $this->string(124),
            'address' => $this->string(1024),
            'birth_date' => $this->integer(),
            'gender' => $this->integer(1),
            'phone' => $this->string(24),
            'phone_optional' => $this->string(24),
            'snils' => $this->string(16),
            'info' => $this->text(),
            'email' => $this->string(124),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('user_id', 'user_common', 'user_id');
        $this->addCommentOnTable('user_common', 'Общие данные');
        $this->db->createCommand()->resetSequence('user_common', 1000)->execute();

    }

    public function down()
    {
        $this->dropTableWithHistory('user_common');
        $this->dropTable('users');
    }
}
