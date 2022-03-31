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
            'users_id' => $this->integer()->notNull(),
            'key_w26' => $this->char(16)->notNull()->comment('Пропуск (в формате W26)'),
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
        $this->addForeignKey('users_card_ibfk_2', 'users_card', 'users_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('users_card_log', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer()->notNull(),
            'key_w26' => $this->string(16)->notNull()->comment('Пропуск (в формате W26)'),
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

    }

    public function down()
    {
        $this->dropTable('users_card_log');
        $this->dropTable('users_card');
    }
}
