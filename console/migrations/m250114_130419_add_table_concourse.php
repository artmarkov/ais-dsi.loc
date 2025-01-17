<?php


class m250114_130419_add_table_concourse extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('concourse', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'name' => $this->string(127)->notNull()->comment('Название конкурса'),
            'users_list' => $this->string(2048)->comment('Список участников'),
            'description' => $this->string()->comment('Описание конкурса'),
            'timestamp_in' => $this->integer()->notNull()->comment('Начало действия'),
            'timestamp_out' => $this->integer()->notNull()->comment('Окончание действия'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('Статус формы (Активная, Не активная)'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('concourse', 'Форма конкурса');

        $this->addForeignKey('concourse_ibfk_1', 'concourse', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('concourse_ibfk_2', 'concourse', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('concourse_ibfk_3', 'concourse', 'author_id', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('concourse_item', [
            'id' => $this->primaryKey(),
            'concourse_id' => $this->integer()->notNull(),
            'authors_list' => $this->string(1024)->comment('Авторы'),
            'name' => $this->string()->comment('Название работы'),
            'description' => $this->text()->comment('Описание работы'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('concourse_item', 'Конкурсные работы');

        $this->addForeignKey('concourse_attribute_ibfk_1', 'concourse_item', 'concourse_id', 'concourse', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('concourse_attribute_ibfk_2', 'concourse_item', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('concourse_attribute_ibfk_3', 'concourse_item', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('concourse_criteria', [
            'id' => $this->primaryKey(),
            'concourse_id' => $this->integer()->notNull(),
            'name' => $this->string()->comment('Название критерия'),
            'name_dev' => $this->string()->comment('Сокращенное название критерия'),
            'sort_order' => $this->integer()->defaultValue(null),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('concourse_criteria', 'Критерии оценки конкурса');

        $this->addForeignKey('concourse_criteria_ibfk_1', 'concourse_criteria', 'concourse_id', 'concourse', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('concourse_criteria_ibfk_2', 'concourse_criteria', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('concourse_criteria_ibfk_3', 'concourse_criteria', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('concourse_value', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer()->notNull(),
            'concourse_item_id' => $this->integer()->notNull(),
            'concourse_criteria_id' => $this->integer()->notNull(),
            'concourse_mark' => $this->integer(),
            'concourse_string' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('concourse_value', 'Оценки работ');
        $this->addForeignKey('concourse_value_ibfk_1', 'concourse_value', 'users_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('concourse_value_ibfk_2', 'concourse_value', 'concourse_item_id', 'concourse_item', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('concourse_value_ibfk_3', 'concourse_value', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('concourse_value_ibfk_4', 'concourse_value', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->createIndex('concourse_value_users_id_concourse_item_id_concourse_criter_key', 'concourse_value', ['users_id', 'concourse_item_id', 'concourse_criteria_id'], true);

    }

    public function down()
    {
        $this->dropTableWithHistory('concourse_value');
        $this->dropTable('concourse_criteria');
        $this->dropTable('concourse_item');
        $this->dropTable('concourse');
    }
}
