<?php


class m220518_100419_add_table_question extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('question', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'name' => $this->string(127)->notNull()->comment('Название формы'),
            'category_id' => $this->integer()->notNull()->defaultValue(1)->comment('Категория формы (Опрос, Заявка)'),
            'users_cat' => $this->integer()->comment('Группа пользователей (Сотрудники, Преподаватели, Ученики, Родители, Гости)'),
            'moderator_list' => $this->string(1024)->comment('Список модераторов'),
            'vid_id' => $this->integer()->notNull()->defaultValue(1)->comment('Вид формы (Открытая, Закрытая)'),
            'division_list' => $this->string(1024)->comment('Список отделений'),
            'description' => $this->string()->comment('Описание формы'),
            'timestamp_in' => $this->integer()->notNull()->comment('Начало действия формы'),
            'timestamp_out' => $this->integer()->notNull()->comment('Окончание действия формы'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('Статус формы (Активная, Не активная)'),
            'email_flag' => $this->integer()->comment('Отправлять пользователям информацию на E-mail при наличии формы?'),
            'email_author_flag' => $this->integer()->comment('Отправлять автору формы информацию на E-mail при каждом заполнении?'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('question', 'Формы опроса и заявок');

        $this->addForeignKey('question_ibfk_1', 'question', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('question_ibfk_2', 'question', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('question_ibfk_3', 'question', 'author_id', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('question_attribute', [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'type_id' => $this->integer()->notNull()->comment('Тип атрибута формы (Строка, Текст, Дата, Дата:время, E-mail, Телефон, Радио-лист, Чек-лист, Файл)'),
            'name' => $this->string(127)->notNull()->comment('Название поля формы(en)'),
            'label' => $this->string(127)->notNull()->comment('Название атрибута формы'),
            'description' => $this->string(1024)->comment('Описание Поля'),
            'hint' => $this->string(512)->comment('Подсказка атрибута формы'),
            'required' => $this->integer()->notNull()->comment('Обязательность атрибута (Да, Нет)'),
            'default_value' => $this->string(127)->defaultValue(null),
            'sort_order' => $this->integer()->defaultValue(null),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('question_attribute', 'Атрибуты формы');

        $this->addForeignKey('question_attribute_ibfk_1', 'question_attribute', 'question_id', 'question', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('question_attribute_ibfk_2', 'question_attribute', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('question_attribute_ibfk_3', 'question_attribute', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('question_options', [
            'id' => $this->primaryKey(),
            'attribute_id' => $this->integer()->notNull(),
            'name' => $this->string()->comment('Название опции атрибута'),
            'free_flag' => $this->integer()->defaultValue(0)->comment('Свободная строка'),
            'sort_order' => $this->integer()->defaultValue(null),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('question_options', 'Опции атрибута формы');

        $this->addForeignKey('question_options_ibfk_1', 'question_options', 'attribute_id', 'question_attribute', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('question_options_ibfk_2', 'question_options', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('question_options_ibfk_3', 'question_options', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('question_users', [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'users_id' => $this->integer()->notNull()->defaultValue(0),
            'read_flag' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('question_users', 'Участники опроса');
        $this->addForeignKey('question_users_ibfk_1', 'question_users', 'question_id', 'question', 'id', 'CASCADE', 'CASCADE');


        $this->createTable('question_value', [
            'id' => $this->primaryKey(),
            'question_users_id' => $this->integer()->notNull(),
            'question_attribute_id' => $this->integer()->notNull(),
            'question_option_list' => $this->string(1024),
            'value_string' => $this->string(1024),
            'value_file' => $this->binary(),
        ], $tableOptions);

        $this->addCommentOnTable('question_value', 'Ответы');
        $this->addForeignKey('question_value_ibfk_1', 'question_value', 'question_users_id', 'question_users', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('question_value_ibfk_2', 'question_value', 'question_attribute_id', 'question_attribute', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('question_value');
        $this->dropTable('question_users');
        $this->dropTableWithHistory('question_options');
        $this->dropTableWithHistory('question_attribute');
        $this->dropTableWithHistory('question');
    }
}
