<?php


class m220330_222515_add_sigur_tables extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('users_card', [
            'id' => $this->primaryKey(),
            'user_common_id' => $this->string(5)->defaultValue(null),
            'key_hex' => $this->char(8)->defaultValue(null)->comment('Пропуск (в формате HEX)'),
            'timestamp_deny' => $this->dateTime()->defaultValue(null)->comment('Срок действия в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС'),
            'mode_main' => $this->string(127)->defaultValue(null)->comment('Основной режим'),
            'mode_list' => $this->string(512)->defaultValue(null)->comment('Список режимов'),
            'photo_bin' => $this->binary()->defaultValue(null)->comment('Фотография'),
            'photo_ver' => $this->integer()->defaultValue(null)->comment('Версия фотографии'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('users_card', 'Пропуска СКУД Сигур');
        $this->addForeignKey('users_card_ibfk_1', 'users_card', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('users_card_log', [
            'id' => $this->primaryKey(),
            'user_common_id' => $this->string(4)->defaultValue(null),
            'key_hex' => $this->string(8)->comment('Пропуск (в формате HEX)'),
            'datetime' => $this->dateTime()->comment('Дата и время события в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС'),
            'deny_reason' => $this->string(32)->comment('Код причины запрета доступа'),
            'dir_code' => $this->integer(1)->comment('Код направления прохода (1=выход, 2=вход, 3=неизвестное).'),
            'dir_name' => $this->string(16)->comment('Наименование направления прохода (OUT, IN, UNKNOWN)'),
            'evtype_code' => $this->integer(1)->comment('Тип события (1=проход, 2=запрет)'),
            'evtype_name' => $this->string(16)->comment('Наименование типа события (PASS, DENY)'),
            'name' => $this->string(127)->comment('Имя сотрудника'),
            'position' => $this->string(127)->comment('Должность сотрудника'),
        ], $tableOptions);

        $this->addCommentOnTable('users_card_log', 'Лог проходов посетителей');

        $this->db->createCommand()->createView('users_card_view', '
        select user_common.id AS id,
            CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) as user_name,
            CASE
              WHEN (user_common.user_category = \'employees\') THEN \'Сотрудники АИС\'
              WHEN (user_common.user_category = \'teachers\') THEN \'Преподаватели АИС\'
              WHEN (user_common.user_category = \'students\') THEN \'Ученики АИС\'
              WHEN (user_common.user_category = \'parents\') THEN \'Родители АИС\'
            END AS user_category,
             CASE
              WHEN (user_common.user_category = \'employees\') THEN \'Сотрудник\'
              WHEN (user_common.user_category = \'teachers\') THEN \'Преподаватель\'
              WHEN (user_common.user_category = \'students\') THEN \'Ученик\'
              WHEN (user_common.user_category = \'parents\') THEN \'Родитель\'
            END AS position,
            users_card.timestamp_deny,
            users_card.key_hex,
            users_card.mode_main,
            users_card.mode_list,
            users_card.photo_bin,
            users_card.photo_ver
        from users_card
        inner join user_common on (user_common.id = users_card.user_common_id::int)
        order by user_category, user_name
        ')->execute();

        $this->db->createCommand()->createView('service_card_view', '
       select user_common.id as user_common_id,
            users_card.id as users_card_id,
            user_common.user_category,
            CASE
              WHEN (user_common.user_category = \'employees\') THEN \'Сотрудник\'
              WHEN (user_common.user_category = \'teachers\') THEN \'Преподаватель\'
              WHEN (user_common.user_category = \'students\') THEN \'Ученик\'
              WHEN (user_common.user_category = \'parents\') THEN \'Родитель\'
            END AS user_category_name,
            CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) AS user_name, 
            user_common.phone,
            user_common.phone_optional,
            user_common.email,
            user_common.status,
            users_card.key_hex,
            users_card.timestamp_deny,
            users_card.mode_main,
            users_card.mode_list,
            users_card.photo_bin
        from user_common 
        left join users_card on (user_common.id = users_card.user_common_id::int)
        order by user_category_name, user_name;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('service_card_view')->execute();
        $this->db->createCommand()->dropView('users_card_view')->execute();
        $this->dropTable('users_card_log');
        $this->dropTableWithHistory('users_card');
    }
}
