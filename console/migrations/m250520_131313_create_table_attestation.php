<?php

class m250520_131313_create_table_attestation extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('attestation_items', [
            'id' => $this->primaryKey(),
            'plan_year' => $this->integer(),
            'studyplan_subject_id' => $this->integer()->notNull()->comment('Учебный предмет ученика'),
            'lesson_mark_id' => $this->integer()->comment('Оценка'),
            'mark_rem' => $this->string(127),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('attestation_items', 'Аттестация');

        $this->addForeignKey('attestation_items_ibfk_1', 'attestation_items', 'studyplan_subject_id', 'studyplan_subject', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('attestation_items_ibfk_2', 'attestation_items', 'lesson_mark_id', 'guide_lesson_mark', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->createView('attestation_items_view', '
         SELECT attestation_items.id,
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
    attestation_items.updated_at AS lesson_date
   FROM attestation_items
     JOIN studyplan_subject ON studyplan_subject.id = attestation_items.studyplan_subject_id
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     LEFT JOIN guide_lesson_mark ON guide_lesson_mark.id = attestation_items.lesson_mark_id
  ORDER BY studyplan.plan_year;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('attestation_items_view')->execute();
        $this->dropTableWithHistory('attestation_items');
    }
}
