<?php

class m250429_165652_create_table_planfix extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('guide_planfix_category', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(256)->notNull(),
            'description' => $this->string(1024)->notNull(),
            'roles' => $this->string(1024)->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_planfix_category' ,'Категории работ');

        $this->db->createCommand()->batchInsert('guide_planfix_category', ['id', 'name', 'description','roles'], [
            [1000, 'Разработка нового функционала АИС', '','developer'],
            [1001, 'Доработка существующего функционала АИС', '','developer'],
            [1002, 'Устранение инцидентов(ошибок) АИС', '','developer'],
            [1003, 'Задание администраторам АИС', '','administrator, system'],
            [1004, 'Задание сотрудникам', '', 'employees'],
            [1005, 'Задание руководителям отделов', '', 'department'],
            [1006, 'Задание преподавателям', '', 'teacher'],
        ])->execute();

        $this->db->createCommand()->resetSequence('guide_planfix_category', 1007)->execute();

        $this->createTableWithHistory('planfix', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(512)->notNull()->comment('Название задания'),
            'description' => $this->string(1024)->comment('Описание задания'),
            'planfix_author' => $this->integer()->notNull()->comment('Автор задания'),
            'executors_list' => $this->string(1024)->notNull()->comment('Исполнители задания'),
            'importance' => $this->integer()->notNull()->comment('Приоритет работы(высокий, обычный, низкий)'),
            'planfix_date' => $this->integer()->notNull()->comment('Планируемая дата выполнения'),
            'status' => $this->integer()->notNull()->defaultValue(1)->comment('Статус работы(В работе, Выполнено, Не выполнено)'),
            'status_reason' => $this->string(1024)->comment('Причина невыполнения'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addCommentOnTable('planfix' ,'Планируемая работа');

        $this->addForeignKey('planfix_ibfk_1', 'planfix', 'category_id', 'guide_planfix_category', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('planfix_ibfk_2', 'planfix', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('planfix_ibfk_3', 'planfix', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('planfix_activity', [
            'id' => $this->primaryKey(),
            'planfix_id' => $this->integer()->notNull(),
            'planfix_activity_category' => $this->integer()->notNull()->comment('Категория этапа работы(Отчет по работе, Дополнительное задание, Доработка, Уточнение задания, Приемка работы)'),
            'executor_comment' => $this->string(512)->comment('Комментарий исполнителя'),
            'author_comment' => $this->string(512)->comment('Комментарий автора работы'),
            'activity_status' => $this->integer()->notNull()->defaultValue(1)->comment('Статус этапа работы(В работе, Принято, Отклонено)'),
            'activity_status_reason' => $this->string(1024)->comment('Причина отклонения этапа работы'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('planfix_activity' ,'Этапы работы');

        $this->addForeignKey('planfix_activity_ibfk_1', 'planfix_activity', 'planfix_id', 'planfix', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('planfix_activity_ibfk_2', 'planfix_activity', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('planfix_activity_ibfk_3', 'planfix_activity', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');


        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['planfix_category', 'guide_planfix_category', 'id', 'name', 'name', null, null, 'Категории работ planfix'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'planfix_category'])->execute();
        $this->dropTableWithHistory('planfix_activity');
        $this->dropTableWithHistory('planfix');
        $this->dropTable('guide_planfix_category');
    }
}
