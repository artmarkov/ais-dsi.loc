<?php

class m210824_115637_create_table_studyplan extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('studyplan', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 99999)',
            'student_id' => $this->integer()->notNull(),
            'programm_id' => $this->integer()->notNull(),
            'speciality_id' => $this->integer()->notNull(),
            'course' => $this->integer(),
            'plan_year' => $this->integer(),
            'description' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('studyplan', 'Индивидуальные планы ученика');
        $this->addForeignKey('studyplan_ibfk_1', 'studyplan', 'student_id', 'students', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_ibfk_2', 'studyplan', 'programm_id', 'education_programm', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_ibfk_3', 'studyplan', 'speciality_id', 'education_speciality', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->resetSequence('studyplan', 1000)->execute();

        $this->createTableWithHistory('studyplan_subject', [
            'id' => $this->primaryKey(),
            'studyplan_id' => $this->integer()->notNull(),
            'subject_cat_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer(),
            'subject_type_id' => $this->integer(),
            'week_time' => $this->float(),
            'year_time' => $this->float(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable( 'studyplan_subject', 'Дисциплины индивидуального плана');
        $this->createIndex('studyplan_id', 'studyplan_subject', 'studyplan_id');
        $this->addForeignKey('studyplan_subject_ibfk_1', 'studyplan_subject', 'studyplan_id', 'studyplan', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_subject_ibfk_2', 'studyplan_subject', 'subject_cat_id', 'guide_subject_category', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_subject_ibfk_3', 'studyplan_subject', 'subject_id', 'subject', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_subject_ibfk_4', 'studyplan_subject', 'subject_type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');


    }

    public function down()
    {
        $this->dropForeignKey('studyplan_subject_ibfk_1', 'studyplan_subject');
        $this->dropForeignKey('studyplan_subject_ibfk_2', 'studyplan_subject');
        $this->dropForeignKey('studyplan_subject_ibfk_3', 'studyplan_subject');
        $this->dropForeignKey('studyplan_subject_ibfk_4', 'studyplan_subject');
        $this->dropForeignKey('studyplan_ibfk_1', 'studyplan');
        $this->dropForeignKey('studyplan_ibfk_2', 'studyplan');
        $this->dropForeignKey('studyplan_ibfk_3', 'studyplan');
        $this->dropTableWithHistory('studyplan_subject');
        $this->dropTableWithHistory('studyplan');

    }
}