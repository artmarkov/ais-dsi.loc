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
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'student_id' => $this->integer()->notNull(),
            'programm_id' => $this->integer()->notNull(),
            'subject_form_id' => $this->integer(),
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
            'mat_capital_flag' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'status_reason' => $this->integer()->defaultValue(0),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('studyplan', 'Планы учащихся');
        $this->addForeignKey('studyplan_ibfk_1', 'studyplan', 'student_id', 'students', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_ibfk_2', 'studyplan', 'programm_id', 'education_programm', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_ibfk_3', 'studyplan', 'subject_form_id', 'guide_subject_form', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->resetSequence('studyplan', 10000)->execute();

        $this->createTableWithHistory('subject_sect', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'programm_list' => $this->text()->comment('Учебные рограммы'),
            'term_mastering' => $this->integer()->comment('Период обучения'),
            'course_list' => $this->text()->comment('Ограничения по классам'),
            'subject_cat_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'subject_vid_id' => $this->integer()->notNull(),
            'subject_type_id' => $this->integer()->notNull(),
            'sect_name' => $this->string(127)->notNull()->comment('Название группы'),
            'course_flag' => $this->integer()->notNull()->comment('Распределить по годам обучения(Да/Нет)'),
            'class_index' => $this->string(32)->comment('Индекс курса'),
            'description' => $this->string(1024)->comment('Описание группы'),
            'sub_group_qty' => $this->integer()->notNull()->comment('Кол-во подгрупп'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject_sect', 'Учебные группы');
        $this->db->createCommand()->resetSequence('subject_sect', 1000)->execute();

        $this->createIndex('subject_cat_id', 'subject_sect', 'subject_cat_id');
        $this->createIndex('subject_id', 'subject_sect', 'subject_id');
        $this->createIndex('subject_type_id', 'subject_sect', 'subject_type_id');
        $this->createIndex('subject_vid_id', 'subject_sect', 'subject_vid_id');
        $this->addForeignKey('subject_sect_ibfk_1', 'subject_sect', 'subject_cat_id', 'guide_subject_category', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_sect_ibfk_2', 'subject_sect', 'subject_id', 'subject', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_sect_ibfk_3', 'subject_sect', 'subject_type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_sect_ibfk_4', 'subject_sect', 'subject_vid_id', 'guide_subject_vid', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('subject_sect_studyplan', [
            'id' => $this->primaryKey(),
            'subject_sect_id' => $this->integer(),
            'subject_type_id' => $this->integer()->notNull(),
            'plan_year' => $this->integer()->notNull(),
            'group_num' => $this->integer()->notNull(),
            'course' => $this->integer(),
            'studyplan_subject_list' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject_sect_studyplan', 'Распределение по учебным группам');

        $this->createIndex('subject_sect_id', 'subject_sect_studyplan', 'subject_sect_id');
        $this->createIndex('plan_year', 'subject_sect_studyplan', 'plan_year');
        $this->createIndex('course', 'subject_sect_studyplan', 'course');

        $this->addForeignKey('subject_sect_studyplan_ibfk_1', 'subject_sect_studyplan', 'subject_sect_id', 'subject_sect', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('subject_sect_studyplan_ibfk_2', 'subject_sect_studyplan', 'subject_type_id', 'guide_subject_type', 'id', 'NO ACTION', 'NO ACTION');

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
            'med_cert' => $this->boolean()->comment('Флаг промежуточной аттестации'),
            'fin_cert' => $this->boolean()->comment('Флаг итоговой аттестации'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable( 'studyplan_subject', 'Предметы плана учащегося');
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
        $this->dropForeignKey('subject_sect_studyplan_ibfk_2', 'subject_sect_studyplan');
        $this->dropForeignKey('subject_sect_studyplan_ibfk_1', 'subject_sect_studyplan');
        $this->dropForeignKey('subject_sect_ibfk_1', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_2', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_3', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_4', 'subject_sect');
        $this->dropForeignKey('studyplan_ibfk_1', 'studyplan');
        $this->dropForeignKey('studyplan_ibfk_2', 'studyplan');
        $this->dropForeignKey('studyplan_ibfk_3', 'studyplan');
        $this->dropTableWithHistory('studyplan_subject');
        $this->dropTableWithHistory('subject_sect_studyplan');
        $this->dropTableWithHistory('subject_sect');
        $this->dropTableWithHistory('studyplan');

    }
}