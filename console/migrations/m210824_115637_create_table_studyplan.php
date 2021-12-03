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
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'student_id' => $this->integer()->notNull(),
            'programm_id' => $this->integer()->notNull(),
            'speciality_id' => $this->integer()->notNull(),
            'course' => $this->integer(),
            'plan_year' => $this->integer(),
            'description' => $this->string(1024),
            'year_time_total' => $this->float()->defaultValue(0),
            'cost_month_total' => $this->float()->defaultValue(0),
            'cost_year_total' => $this->float()->defaultValue(0),
            'doc_date' => $this->integer(),
            'doc_contract_start' => $this->integer(),
            'doc_contract_end' => $this->integer(),
            'doc_signer' => $this->integer(),
            'doc_received_flag' => $this->integer(),
            'doc_sent_flag' => $this->integer(),
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



        $this->createTableWithHistory('subject_sect', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'plan_year' => $this->integer(),
            'union_id' => $this->integer()->notNull(),
            'course' => $this->integer(),
            'subject_cat_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer(),
            'subject_type_id' => $this->integer(),
            'subject_vid_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject_sect', 'Учебные группы');
        $this->db->createCommand()->resetSequence('subject_sect', 10000)->execute();
        $this->createIndex('plan_year', 'subject_sect', 'plan_year');
        $this->createIndex('course', 'subject_sect', 'course');
        $this->createIndex('subject_cat_id', 'subject_sect', 'subject_cat_id');
        $this->createIndex('subject_id', 'subject_sect', 'subject_id');
        $this->createIndex('subject_type_id', 'subject_sect', 'subject_type_id');
        $this->createIndex('subject_vid_id', 'subject_sect', 'subject_vid_id');
        $this->addForeignKey('subject_sect_ibfk_1', 'subject_sect', 'union_id', 'education_union', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_sect_ibfk_2', 'subject_sect', 'subject_cat_id', 'guide_subject_category', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_sect_ibfk_3', 'subject_sect', 'subject_id', 'subject', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_sect_ibfk_4', 'subject_sect', 'subject_type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_sect_ibfk_5', 'subject_sect', 'subject_vid_id', 'guide_subject_vid', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('subject_sect_studyplan', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'subject_sect_id' => $this->integer(),
            'studyplan_subject_list' => $this->text(),
            'class_name' => $this->string(64),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject_sect_studyplan', 'Ученики учебной группы');
        $this->db->createCommand()->resetSequence('subject_sect_studyplan', 10000)->execute();

        $this->createIndex('subject_sect_id', 'subject_sect_studyplan', 'subject_sect_id');
        $this->addForeignKey('subject_sect_studyplan_ibfk_1', 'subject_sect_studyplan', 'subject_sect_id', 'subject_sect', 'id', 'CASCADE', 'CASCADE');

        $this->createTableWithHistory('studyplan_subject', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'studyplan_id' => $this->integer()->notNull(),
            'subject_cat_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer(),
            'subject_type_id' => $this->integer(),
            'subject_vid_id' => $this->integer(),
            'week_time' => $this->float(),
            'year_time' => $this->float(),
            'cost_hour' => $this->float(),
            'cost_month_summ' => $this->float(),
            'cost_year_summ' => $this->float(),
            'year_time_consult' => $this->float(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable( 'studyplan_subject', 'Дисциплины индивидуального плана');
        $this->db->createCommand()->resetSequence('studyplan_subject', 10000)->execute();

        $this->createIndex('studyplan_id', 'studyplan_subject', 'studyplan_id');
        $this->addForeignKey('studyplan_subject_ibfk_1', 'studyplan_subject', 'studyplan_id', 'studyplan', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('studyplan_subject_ibfk_2', 'studyplan_subject', 'subject_cat_id', 'guide_subject_category', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_subject_ibfk_3', 'studyplan_subject', 'subject_id', 'subject', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_subject_ibfk_4', 'studyplan_subject', 'subject_type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_subject_ibfk_5', 'studyplan_subject', 'subject_vid_id', 'guide_subject_vid', 'id', 'NO ACTION', 'NO ACTION');


    }

    public function down()
    {
        $this->dropForeignKey('studyplan_subject_ibfk_1', 'studyplan_subject');
        $this->dropForeignKey('studyplan_subject_ibfk_2', 'studyplan_subject');
        $this->dropForeignKey('studyplan_subject_ibfk_3', 'studyplan_subject');
        $this->dropForeignKey('studyplan_subject_ibfk_4', 'studyplan_subject');
        $this->dropForeignKey('studyplan_subject_ibfk_5', 'studyplan_subject');
        $this->dropForeignKey('subject_sect_studyplan_ibfk_1', 'subject_sect_studyplan');
        $this->dropForeignKey('subject_sect_ibfk_1', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_2', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_3', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_4', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_5', 'subject_sect');
        $this->dropForeignKey('studyplan_ibfk_1', 'studyplan');
        $this->dropForeignKey('studyplan_ibfk_2', 'studyplan');
        $this->dropForeignKey('studyplan_ibfk_3', 'studyplan');
        $this->dropTableWithHistory('studyplan_subject');
        $this->dropTableWithHistory('subject_sect_studyplan');
        $this->dropTableWithHistory('subject_sect');
        $this->dropTableWithHistory('studyplan');

    }
}