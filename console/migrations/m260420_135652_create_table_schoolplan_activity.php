<?php

class m260420_135652_create_table_schoolplan_activity extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTableWithHistory('schoolplan_activity', [
            'id' => $this->primaryKey(),
            'schoolplan_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull()->comment('Автор работы'),
            'executor_id' => $this->integer()->notNull()->comment('Исполнитель работы'),
            'datetime_in' => $this->integer()->notNull()->comment('Дата и время работы'),
            'name' => $this->string(512)->notNull()->comment('Название работы'),
            'places' => $this->string(512)->comment('Место работы'),
            'author_comment' => $this->text()->comment('Описание работы'),
            'executor_comment' => $this->text()->comment('Отчет исполнителя работы'),
            'activity_status' => $this->integer()->notNull()->defaultValue(1)->comment('Статус работы(В работе, Выполнено, Не выполнено)'),
            'activity_status_reason' => $this->string(1024)->comment('Причина невыполнения работы'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('schoolplan_activity' ,'План работ ответственных за мероприятие');

        $this->addForeignKey('schoolplan_activity_ibfk_1', 'schoolplan_activity', 'schoolplan_id', 'schoolplan', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('schoolplan_activity_ibfk_2', 'schoolplan_activity', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_activity_ibfk_3', 'schoolplan_activity', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_activity_ibfk_4', 'schoolplan_activity', 'author_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_activity_ibfk_5', 'schoolplan_activity', 'executor_id', 'users', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropTableWithHistory('schoolplan_activity');
    }
}
