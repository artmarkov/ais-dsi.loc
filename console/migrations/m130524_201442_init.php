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

        $this->db->createCommand()->createView('users_view', '
       SELECT users.id,
    users.username,
    users.email,
    users.email_confirmed,
    users.superadmin,
    (SELECT array_to_string((( SELECT array_agg(auth_item.description) AS array_agg
                   FROM auth_item
                     JOIN auth_assignment ON auth_assignment.item_name::text = auth_item.name::text
                  WHERE auth_assignment.user_id = users.id))::character varying[], \',\'::text) AS array_to_string) AS roles,
    users.registration_ip,
    users.status,
    user_common.id AS user_common_id,
        CASE
            WHEN user_common.user_category::text = \'employees\'::text THEN \'Сотрудник\'::text
            WHEN user_common.user_category::text = \'teachers\'::text THEN \'Преподаватель\'::text
            WHEN user_common.user_category::text = \'students\'::text THEN \'Ученик\'::text
            WHEN user_common.user_category::text = \'parents\'::text THEN \'Родитель\'::text
            WHEN user_common.user_category IS NULL THEN \'Система\'::text
            ELSE NULL::text
        END AS user_category_name,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS user_name,
    user_common.phone,
    user_common.phone_optional,
    user_common.status AS user_common_status
   FROM users
     LEFT JOIN user_common ON user_common.user_id = users.id
  ORDER BY users.id;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('users_view')->execute();
        $this->dropTableWithHistory('user_common');
        $this->dropTable('users');
    }
}
