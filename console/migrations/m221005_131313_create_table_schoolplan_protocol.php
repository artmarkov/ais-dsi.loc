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
            'schoolplan_id' => $this->integer()->comment('Мероприятие'),
            'studyplan_id' => $this->integer()->comment('Индивидуальный план'),
            'studyplan_subject_id' => $this->integer()->comment('Учебный предмет ученика'),
            'teachers_id' => $this->integer()->notNull()->comment('Преподаватель'),
            'thematic_items_list' => $this->string(1024)->comment('Список заданий из тематич/реп плана'),
            'lesson_mark_id' =>  $this->integer()->comment('Оценка'),
            'winner_id' => $this->string()->defaultValue(0)->comment('Звание/Диплом'),
            'resume' => $this->string(1024)->comment('Результат'),
            'status_exe' => $this->integer()->notNull()->comment('Статус выполнения'),
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
         SELECT schoolplan_protocol.id AS schoolplan_protocol_id,
    studyplan_subject.id AS studyplan_subject_id,
    guide_lesson_mark.id AS lesson_mark_id,
    guide_lesson_mark.mark_category,
    guide_lesson_mark.mark_label,
    guide_lesson_mark.mark_hint,
    guide_lesson_mark.mark_value,
    concat(studyplan_subject.subject_id, \'|\', studyplan_subject.subject_vid_id, \'|\', studyplan_subject.subject_type_id, \'|\', education_programm.education_cat_id) AS subject_key,
    studyplan.id AS studyplan_id,
    studyplan.status,
    studyplan.status_reason,
    studyplan.plan_year,
    studyplan_subject.subject_id,
    studyplan_subject.subject_vid_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_cat_id,
    studyplan_subject.med_cert,
    studyplan_subject.fin_cert,
    schoolplan_protocol.updated_at AS lesson_date
   FROM schoolplan_protocol
     JOIN studyplan_subject ON studyplan_subject.id = schoolplan_protocol.studyplan_subject_id
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     LEFT JOIN guide_lesson_mark ON guide_lesson_mark.id = schoolplan_protocol.lesson_mark_id
  ORDER BY studyplan.plan_year;
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
