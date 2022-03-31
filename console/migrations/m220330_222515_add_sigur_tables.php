<?php


class m220330_222515_add_sigur_tables extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('users_card', [
            'id' => $this->primaryKey(),
            'user_common_id' => $this->integer()->notNull(),
            'key_dec' => $this->char(8)->notNull()->comment('Пропуск (в формате DEC)'),
            'timestamp_deny' => $this->dateTime()->comment('Срок действия в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС'),
            'mode_main' => $this->string(127)->comment('Основной режим'),
            'mode_list' => $this->string(512)->comment('Список режимов'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('users_card', 'Пропуска СКУД Сигур');
        $this->addForeignKey('users_card_ibfk_1', 'users_card', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('users_card_ibfk_2', 'users_card', 'user_common_id', 'user_common', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('users_card_log', [
            'id' => $this->primaryKey(),
            'user_common_id' => $this->integer()->notNull(),
            'key_dec' => $this->char(8)->notNull()->comment('Пропуск (в формате DEC)'),
            'datetime' => $this->dateTime()->comment('Дата и время события в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС'),
            'deny_reason' => $this->char(32)->comment('Код причины запрета доступа'),
            'dir_code' => $this->integer(1)->comment('Код направления прохода (1=выход, 2=вход, 3=неизвестное).'),
            'dir_name' => $this->string(16)->comment('Наименование направления прохода (OUT, IN, UNKNOWN)'),
            'evtype_code' => $this->integer(1)->comment('Тип события (1=проход, 2=запрет)'),
            'evtype_name' => $this->string(16)->comment('Наименование типа события (PASS, DENY)'),
            'name' => $this->string(127)->comment('Имя сотрудника'),
            'position' => $this->string(127)->comment('Должность сотрудника'),
        ], $tableOptions);

        $this->addCommentOnTable('users_card_log', 'Лог проходов посетителей');

        $this->db->createCommand()->createView('users_card_view', '
        select user_common.id as id,
            CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) as user_name,
            CASE
              WHEN (user_common.user_category = \'employees\') THEN \'Сотрудники АИС\'
              WHEN (user_common.user_category = \'teachers\') THEN \'Преподаватели АИС\'
              WHEN (user_common.user_category = \'students\') THEN \'Ученики АИС\'
              WHEN (user_common.user_category = \'parents\') THEN \'Родители АИС\'
            END AS user_category,
            users_card.timestamp_deny,
            users_card.key_dec,
            users_card.mode_main,
            users_card.mode_list
        from users_card
        inner join user_common on (user_common.id = users_card.user_common_id)
        order by user_category, user_name
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('users_card_view')->execute();
        $this->dropTable('users_card_log');
        $this->dropTable('users_card');
    }
}
