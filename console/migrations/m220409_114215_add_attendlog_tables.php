<?php


class m220409_114215_add_attendlog_tables extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('users_attendlog', [
            'id' => $this->primaryKey(),
            'user_common_id' => $this->integer()->notNull(),
            'timestamp' => $this->integer()->notNull()->comment('Дата записи'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('users_attendlog', 'Журнал выдачи ключей');
        $this->addForeignKey('users_attendlog_ibfk_1', 'users_attendlog', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('users_attendlog_ibfk_2', 'users_attendlog', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('users_attendlog_ibfk_3', 'users_attendlog', 'user_common_id', 'user_common', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('users_attendlog_key', [
            'id' => $this->primaryKey(),
            'users_attendlog_id' => $this->integer()->notNull(),
            'auditory_id' => $this->integer()->notNull(),
            'timestamp_received' => $this->integer()->notNull()->comment('Ключ выдан'),
            'timestamp_over' => $this->integer()->comment('Ключ сдан'),
            'comment' => $this->string(127),
            'key_free_flag' => $this->boolean()->defaultValue(false)->comment('Занимаются вместе'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('users_attendlog', 'Журнал выдачи ключей');
        $this->addForeignKey('users_attendlog_key_ibfk_1', 'users_attendlog_key', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('users_attendlog_key_ibfk_2', 'users_attendlog_key', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('users_attendlog_key_ibfk_3', 'users_attendlog_key', 'users_attendlog_id', 'users_attendlog', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('users_attendlog_key_ibfk_4', 'users_attendlog_key', 'auditory_id', 'auditory', 'id', 'NO ACTION', 'NO ACTION');


        $this->db->createCommand()->createView('users_attendlog_view', '
        select users_attendlog_key.id as id,
            users_attendlog.id as users_attendlog_id,
            user_common.user_category,
            CASE
              WHEN (user_common.user_category = \'employees\') THEN \'Сотрудник\'
              WHEN (user_common.user_category = \'teachers\') THEN \'Преподаватель\'
              WHEN (user_common.user_category = \'students\') THEN \'Ученик\'
              WHEN (user_common.user_category = \'parents\') THEN \'Родитель\'
            END AS user_category_name,
            user_common.id AS user_common_id,
            CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) as user_name,
            users_attendlog_key.auditory_id,
            users_attendlog_key.timestamp_received,
            users_attendlog_key.timestamp_over,
            users_attendlog_key.comment,
            users_attendlog.timestamp
        from users_attendlog
        inner join users_attendlog_key on(users_attendlog_key.users_attendlog_id = users_attendlog.id)
        inner join user_common on (user_common.id = users_attendlog.user_common_id)
        order by timestamp, user_category, user_name
        ')->execute();

    }

    public function down()
    {
        $this->db->createCommand()->dropView('users_attendlog_view')->execute();
        $this->dropTable('users_attendlog_key');
        $this->dropTable('users_attendlog');
    }
}
