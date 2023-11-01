<?php

class m221005_131313_create_table_schoolplan_protocol extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('schoolplan_perform', [
            'id' => $this->primaryKey(),
            'schoolplan_id' => $this->integer()->defaultValue(0)->comment('Мероприятие'),
            'studyplan_id' => $this->integer()->notNull()->comment('Индивидуальный план'),
            'studyplan_subject_id' => $this->integer()->notNull()->comment('Учебный предмет ученика'),
            'teachers_id' => $this->integer()->notNull()->comment('Преподаватель'),
            'thematic_items_list' => $this->string(1024)->comment('Список заданий из тематич/реп плана'),
            'lesson_mark_id' =>  $this->integer()->comment('Оценка'),
            'winner_id' => $this->string()->defaultValue(0)->comment('Звание/Диплом'),
            'resume' => $this->string(1024)->notNull()->comment('Отзыв комиссии/Результат'),
            'status_exe' => $this->integer()->comment('Статус выполнения'),
            'status_sign' => $this->integer()->defaultValue(0)->comment('Статус утверждения'),
            'signer_id' => $this->integer()->comment('Подписант'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('schoolplan_perform', 'Выполнение плана и участие в мероприятии');

        $this->addForeignKey('schoolplan_perform_ibfk_1', 'schoolplan_perform', 'schoolplan_id', 'schoolplan', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('schoolplan_perform_ibfk_2', 'schoolplan_perform', 'studyplan_id', 'studyplan', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_perform_ibfk_3', 'schoolplan_perform', 'studyplan_subject_id', 'studyplan_subject', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_perform_ibfk_4', 'schoolplan_perform', 'lesson_mark_id', 'guide_lesson_mark', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_perform_ibfk_5', 'schoolplan_perform', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('schoolplan_protocol', [
            'id' => $this->primaryKey(),
            'schoolplan_id' => $this->integer()->defaultValue(0)->comment('Мероприятие'),
            'protocol_name' => $this->string(127)->notNull()->comment('Название протокола'),
            'description' => $this->string(512)->comment('Описание протокола'),
            'protocol_date' => $this->integer()->notNull()->comment('Дата протокола'),
            'leader_id' => $this->integer()->notNull()->comment('Реководитель комиссии user_id'),
            'secretary_id' => $this->integer()->notNull()->comment('Секретарь комиссии user_id'),
            'members_list' => $this->string(1024)->notNull()->comment('Члены комиссии user_id'),
            'subject_list' => $this->string(1024)->notNull()->comment('Дисциплины'),
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
            'studyplan_subject_id' => $this->integer()->notNull()->comment('Учебный предмет ученика'),
            'thematic_items_list' => $this->string(1024)->comment('Список заданий из тематич/реп плана'),
            'lesson_mark_id' =>  $this->integer()->comment('Оценка'),
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
        $this->addForeignKey('schoolplan_protocol_items_ibfk_3', 'schoolplan_protocol_items', 'lesson_mark_id', 'guide_lesson_mark', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->createView('schoolplan_protocol_items_view', '
        SELECT schoolplan_protocol_items.id,
                schoolplan_protocol_items.schoolplan_protocol_id,
                schoolplan_protocol_items.studyplan_subject_id,
                studyplan_subject.studyplan_id,
                schoolplan_protocol_items.thematic_items_list,
                schoolplan_protocol_items.lesson_mark_id,
                schoolplan_protocol_items.winner_id,
                schoolplan_protocol_items.resume,
                schoolplan_protocol_items.status_exe,
                schoolplan_protocol_items.status_sign,
                schoolplan_protocol_items.signer_id,
                schoolplan_protocol.schoolplan_id,
                schoolplan_protocol.protocol_name,
                schoolplan_protocol.protocol_date,
                schoolplan.title,
                schoolplan.datetime_in,
                schoolplan.datetime_out
	FROM schoolplan_protocol_items
	INNER JOIN schoolplan_protocol ON schoolplan_protocol.id = schoolplan_protocol_items.schoolplan_protocol_id
	INNER JOIN schoolplan ON schoolplan.id = schoolplan_protocol.schoolplan_id
	INNER JOIN studyplan_subject ON studyplan_subject.id = schoolplan_protocol_items.studyplan_subject_id
	ORDER BY studyplan_subject.studyplan_id
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('schoolplan_protocol_items_view')->execute();
        $this->dropTableWithHistory('schoolplan_protocol_items');
        $this->dropTableWithHistory('schoolplan_protocol');
        $this->dropTableWithHistory('schoolplan_perform');
    }
}
