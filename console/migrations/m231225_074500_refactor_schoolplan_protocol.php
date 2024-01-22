<?php

/**
 * Class m231225_074500_refactor_schoolplan_protocol
 */
class m231225_074500_refactor_schoolplan_protocol extends \artsoft\db\BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        $this->db->createCommand()->dropView('schoolplan_protocol_items_view')->execute();

        $this->dropForeignKey('schoolplan_protocol_items_ibfk_3', 'schoolplan_protocol_items');
        $this->dropForeignKey('schoolplan_protocol_items_ibfk_2', 'schoolplan_protocol_items');
        $this->dropForeignKey('schoolplan_protocol_items_ibfk_1', 'schoolplan_protocol_items');
        $this->dropTableWithHistory('schoolplan_protocol_items');

        $this->dropForeignKey('schoolplan_protocol_ibfk_3', 'schoolplan_protocol');
        $this->dropForeignKey('schoolplan_protocol_ibfk_2', 'schoolplan_protocol');
        $this->dropForeignKey('schoolplan_protocol_ibfk_1', 'schoolplan_protocol');
        $this->dropTableWithHistory('schoolplan_protocol');
        $this->db->createCommand()->dropView('schoolplan_view')->execute();

        $this->addColumnWithHistory('schoolplan', 'protocol_leader_id', $this->integer()->comment('Председатель комиссии user_id'));
        $this->addColumnWithHistory('schoolplan', 'protocol_leader_name', $this->string(127)->comment('Председатель комиссии(введено вручную)'));
        $this->addColumnWithHistory('schoolplan', 'protocol_soleader_id', $this->integer()->comment('Заместитель председателя комиссии user_id'));
        $this->addColumnWithHistory('schoolplan', 'protocol_secretary_id', $this->integer()->comment('Секретарь комиссии user_id'));
        $this->addColumnWithHistory('schoolplan', 'protocol_members_list', $this->string(1024)->comment('Члены комиссии user_id'));
        $this->addColumnWithHistory('schoolplan', 'protocol_class_list', $this->string(1024)->comment('Классы'));
        $this->addColumnWithHistory('schoolplan', 'protocol_subject_cat_id', $this->integer()->comment('Категория дисциплины'));
        $this->addColumnWithHistory('schoolplan', 'protocol_subject_id', $this->integer()->comment('Дисциплина'));
        $this->addColumnWithHistory('schoolplan', 'protocol_subject_vid_id', $this->integer()->comment('Вид дисциплины(групповое, инд)'));

        $this->addForeignKey('schoolplan_ibfk_7', 'schoolplan', 'protocol_leader_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_ibfk_8', 'schoolplan', 'protocol_secretary_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_ibfk_9', 'schoolplan', 'protocol_soleader_id', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('schoolplan_protocol', [
            'id' => $this->primaryKey(),
            'schoolplan_id' => $this->integer()->comment('Мероприятие'),
            'studyplan_subject_id' => $this->integer()->comment('Учебный предмет ученика'),
            'teachers_id' => $this->integer()->notNull()->comment('Преподаватель'),
            'thematic_items_list' => $this->string(1024)->comment('Список заданий из тематич/реп плана'),
            'task_ticket' => $this->string(127)->comment('Билет-задание'),
            'lesson_mark_id' =>  $this->integer()->comment('Оценка'),
            'resume' => $this->string(1024)->comment('Отзыв комиссии/Результат'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('schoolplan_protocol', 'Протокол мероприятия');

        $this->addForeignKey('schoolplan_protocol_ibfk_1', 'schoolplan_protocol', 'schoolplan_id', 'schoolplan', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('schoolplan_protocol_ibfk_2', 'schoolplan_protocol', 'lesson_mark_id', 'guide_lesson_mark', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_protocol_ibfk_3', 'schoolplan_protocol', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_protocol_ibfk_4', 'schoolplan_protocol', 'studyplan_subject_id', 'studyplan_subject', 'id', 'NO ACTION', 'NO ACTION');


        $this->db->createCommand()->createView('schoolplan_view', '
 SELECT schoolplan.id,
    schoolplan.author_id,
    schoolplan.signer_id,
    schoolplan.title,
    schoolplan.datetime_in,
    schoolplan.datetime_out,
    schoolplan.places,
    schoolplan.auditory_id,
        CASE
            WHEN schoolplan.places IS NOT NULL THEN schoolplan.places
            WHEN schoolplan.auditory_id IS NOT NULL THEN (( SELECT concat(auditory.num, \' - \', auditory.name) AS concat
               FROM auditory
              WHERE auditory.id = schoolplan.auditory_id))::character varying
            ELSE NULL::character varying
        END AS auditory_places,
    schoolplan.department_list,
    schoolplan.executors_list,
    schoolplan.category_id,
    schoolplan.activities_over_id,
    schoolplan.form_partic,
    schoolplan.partic_price,
    schoolplan.visit_poss,
    schoolplan.visit_content,
    schoolplan.format_event,
    schoolplan.important_event,
    schoolplan.region_partners,
    schoolplan.site_url,
    schoolplan.site_media,
    schoolplan.description,
    schoolplan.rider,
    schoolplan.result,
    schoolplan.num_users,
    schoolplan.num_winners,
    schoolplan.num_visitors,
        CASE
            WHEN schoolplan.bars_flag = true THEN 1
            ELSE 0
        END AS bars_flag,
    schoolplan.created_at,
    schoolplan.created_by,
    schoolplan.updated_at,
    schoolplan.updated_by,
    schoolplan.version,
    schoolplan.doc_status,
    schoolplan.protocol_leader_id,
    schoolplan.protocol_leader_name,
    schoolplan.protocol_soleader_id,
    schoolplan.protocol_secretary_id,
    schoolplan.protocol_members_list,
    schoolplan.protocol_class_list,
    schoolplan.protocol_subject_cat_id,
    schoolplan.protocol_subject_id,
    schoolplan.protocol_subject_vid_id
   FROM schoolplan;
   ')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}
