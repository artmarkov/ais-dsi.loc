<?php


class m211119_191543_ref extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTableWithHistory('education_union', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'union_name' => $this->string(64),
            'programm_list' => $this->text()->notNull(),
            'class_index' => $this->string(32),
            'description' => $this->string(1024)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_union', 'Объединение учебных планов'); // включает в себя учебные планы под одно название
        $this->db->createCommand()->resetSequence('education_union', 1000)->execute();


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
            'studyplan_list' => $this->text(),
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

        $this->createTableWithHistory('teachers_load', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'sect_id' => $this->integer(),
            'direction_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'week_time' => $this->float(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_load', 'Нагрузка преподавателя');
        $this->db->createCommand()->resetSequence('teachers_load', 10000)->execute();
        $this->createIndex('sect_id', 'teachers_load', 'sect_id');
        $this->createIndex('teachers_id', 'teachers_load', 'teachers_id');
        $this->addForeignKey('teachers_load_ibfk_1', 'teachers_load', 'sect_id', 'subject_sect', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_load_ibfk_2', 'teachers_load', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_load_ibfk_3', 'teachers_load', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('sect_schedule', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'sect_id' => $this->integer(),
            'direction_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'week_num' => $this->integer(),
            'week_day' => $this->integer(),
            'time_in' => $this->integer(),
            'time_out' => $this->integer(),
            'auditory_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('sect_schedule', 'Расписание занятий');
        $this->db->createCommand()->resetSequence('sect_schedule', 10000)->execute();
        $this->addForeignKey('sect_schedule_ibfk_1', 'sect_schedule', 'sect_id', 'subject_sect', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('sect_schedule_ibfk_2', 'sect_schedule', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('sect_schedule_ibfk_3', 'sect_schedule', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('teachers_plan', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'direction_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'plan_year' => $this->integer(),
            'week_num' => $this->integer(),
            'week_day' => $this->integer(),
            'time_in' => $this->integer(),
            'time_out' => $this->integer(),
            'auditory_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_plan', 'Планирование инд. занятий преподавателя');
        $this->db->createCommand()->resetSequence('sect_schedule', 10000)->execute();
        $this->addForeignKey('teachers_plan_ibfk_1', 'teachers_plan', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_plan_ibfk_2', 'teachers_plan', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['users_teachers', 'teachers_view', 'user_id', 'teachers_id', 'user_id', 'status', null, 'Преподаватели (ссылка на id учетной записи)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['union_name', 'education_union', 'id', 'union_name', 'union_name', 'status', null, 'Объединения программ'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_name', 'subject', 'id', 'name', 'name', 'status', null, 'Дисциплины(полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_name_dev', 'subject', 'id', 'slug', 'slug', 'status', null, 'Дисциплины(сокр)'],
        ])->execute();

        $this->db->createCommand()->createView('studyplan_subject_view', '
         select studyplan_subject.id as studyplan_subject_id,
                studyplan_subject.studyplan_id as studyplan_id,
                studyplan.student_id as student_id,
                studyplan.course as course,
                studyplan.plan_year as plan_year,
                guide_subject_category.name as subject_category_name,
                guide_subject_category.slug as subject_category_slug,
                subject.name as subject_name,
                subject.slug as subject_slug,
                guide_subject_vid.name as subject_vid_name,
                guide_subject_vid.slug as subject_vid_slug,
                guide_subject_type.name as subject_type_name,
                guide_subject_type.slug as subject_type_slug,
                education_programm.name as education_programm_name,
                education_programm.short_name as education_programm_short_name,
                guide_education_cat.name as education_cat_name,
                guide_education_cat.short_name as education_cat_short_name,
                concat(subject.name, \'(\',guide_subject_vid.slug, \' \',guide_subject_type.slug,\') \',guide_education_cat.short_name) as memo_1
            from studyplan_subject
            inner join studyplan on studyplan.id = studyplan_subject.studyplan_id
            inner join education_programm on education_programm.id = studyplan.programm_id
            inner join guide_education_cat on guide_education_cat.id = education_programm.education_cat_id
            inner join guide_subject_category on guide_subject_category.id = studyplan_subject.subject_cat_id
            inner join subject on subject.id = studyplan_subject.subject_id
            inner join guide_subject_vid on guide_subject_vid.id = studyplan_subject.subject_vid_id
            inner join guide_subject_type on guide_subject_type.id = studyplan_subject.subject_type_id
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['memo_1', 'studyplan_subject_view', 'studyplan_subject_id', 'memo_1', 'studyplan_id', null, null, 'Ученики (Фамилия И.О.)'],
        ])->execute();
    }

    public function down()
    {
//        $this->db->createCommand()->delete('refbooks', ['name' => 'memo_1'])->execute();
//        $this->db->createCommand()->dropView('studyplan_subject_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'union_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'users_teachers'])->execute();

        $this->dropForeignKey('teachers_plan_ibfk_1', 'teachers_plan');
        $this->dropForeignKey('teachers_plan_ibfk_2', 'teachers_plan');
        $this->dropForeignKey('sect_schedule_ibfk_1', 'sect_schedule');
        $this->dropForeignKey('sect_schedule_ibfk_2', 'sect_schedule');
        $this->dropForeignKey('sect_schedule_ibfk_3', 'sect_schedule');
        $this->dropForeignKey('teachers_load_ibfk_1', 'teachers_load');
        $this->dropForeignKey('teachers_load_ibfk_2', 'teachers_load');
        $this->dropForeignKey('teachers_load_ibfk_3', 'teachers_load');
        $this->dropForeignKey('subject_sect_studyplan_ibfk_1', 'subject_sect_studyplan');
        $this->dropForeignKey('subject_sect_ibfk_1', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_2', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_3', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_4', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_5', 'subject_sect');
        $this->dropTableWithHistory('teachers_plan');
        $this->dropTableWithHistory('sect_schedule');
        $this->dropTableWithHistory('teachers_load');
        $this->dropTableWithHistory('subject_sect_studyplan');
        $this->dropTableWithHistory('subject_sect');
        $this->dropTableWithHistory('education_union');
    }
}
