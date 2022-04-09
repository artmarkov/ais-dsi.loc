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
            'auditory_id' => $this->integer()->notNull(),
            'timestamp_received' => $this->integer()->notNull()->comment('Ключ выдан'),
            'timestamp_over' => $this->integer()->comment('Ключ сдан'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('users_attendlog', 'Журнал выдачи ключей');
        $this->addForeignKey('users_card_ibfk_1', 'users_attendlog', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('users_card_ibfk_2', 'users_attendlog', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('users_card_ibfk_3', 'users_attendlog', 'user_common_id', 'user_common', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('users_card_ibfk_4', 'users_attendlog', 'auditory_id', 'auditory', 'id', 'NO ACTION', 'NO ACTION');


        $this->db->createCommand()->createView('users_attendlog_view', '
        select user_common.id AS id,
            CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) as user_name,
            user_common.user_category,
            CASE
              WHEN (user_common.user_category = \'employees\') THEN \'Сотрудники\'
              WHEN (user_common.user_category = \'teachers\') THEN \'Преподаватели\'
              WHEN (user_common.user_category = \'students\') THEN \'Ученики\'
              WHEN (user_common.user_category = \'parents\') THEN \'Родители\'
            END AS user_category_name,
            users_attendlog.auditory_id,
            users_attendlog.timestamp_received,
            users_attendlog.timestamp_over
        from users_attendlog
        inner join user_common on (user_common.id = users_attendlog.user_common_id)
        order by user_category, user_name
        ')->execute();

    }

    public function down()
    {
        $this->db->createCommand()->dropView('users_attendlog_view')->execute();
        $this->dropTable('users_attendlog');
    }
}
