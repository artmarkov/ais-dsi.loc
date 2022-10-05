<?php

class m221005_131313_create_table_schoolplan_protocol extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('schoolplan_protocol', [
            'id' => $this->primaryKey(),
            'schoolplan_id' => $this->integer()->defaultValue(0)->comment('Мероприятие'),
            'protocol_name' => $this->string(127)->notNull()->comment('Название протокола'),
            'description' => $this->string(512)->comment('Описание протокола'),
            'protocol_date' => $this->integer()->notNull()->comment('Дата протокола'),
            'leader_id' => $this->integer()->notNull()->comment('Реководитель комиссии user_id'),
            'secretary_id' => $this->integer()->notNull()->comment('Секретарь комиссии user_id'),
            'members_list' => $this->string(1024)->notNull()->comment('Члены комиссии user_id'),
            'subject_list' => $this->integer()->comment('Дисциплины'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('schoolplan_protocol', 'Протоколы мероприятий');

        $this->addForeignKey('schoolplan_protocol_ibfk_1', 'schoolplan_protocol', 'schoolplan_id', 'schoolplan', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('schoolplan_protocol_ibfk_2', 'schoolplan_protocol', 'leader_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_protocol_ibfk_3', 'schoolplan_protocol', 'secretary_id', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('schoolplan_protocol_items', [
            'id' => $this->primaryKey(),
            'schoolplan_protocol_id' => $this->integer()->defaultValue(0)->comment('Протокол'),
            'studyplan_subject_id' => $this->integer()->notNull()->comment('Дисциплина ученика'),
            'thematic_items_list' => $this->string(1024)->comment('Список заданий из тематич/реп плана'),
            'lesson_progress_id' => $this->integer()->notNull()->comment('Связь с уроком и оценкой'),
            'winner_id' => $this->string()->defaultValue(0)->comment('Звание/Диплом'),
            'resume' => $this->string(1024)->notNull()->comment('Отзыв комиссии/Результат'),
            'status_exe' => $this->integer()->comment('Статус выполнения'),
            'status_sign' => $this->integer()->comment('Статус утверждения'),
            'signer_id' => $this->integer()->comment('Подписант'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('schoolplan_protocol_items', 'Элементы протокола мероприятия');

        $this->addForeignKey('schoolplan_protocol_items_ibfk_1', 'schoolplan_protocol_items', 'schoolplan_protocol_id', 'schoolplan_protocol', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('schoolplan_protocol_items_ibfk_2', 'schoolplan_protocol_items', 'studyplan_subject_id', 'studyplan_subject', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_protocol_items_ibfk_3', 'schoolplan_protocol_items', 'lesson_progress_id', 'lesson_progress', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropTableWithHistory('schoolplan_protocol_items');
        $this->dropTableWithHistory('schoolplan_protocol');
    }
}
