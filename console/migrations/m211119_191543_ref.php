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
            'description' => $this->string(1024)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_union', 'Объединение учебных программ'); // включает в себя учебные программы под одно название
        $this->db->createCommand()->resetSequence('education_union', 1000)->execute();


        $this->createTableWithHistory('subject_sect', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'plan_year' => $this->integer(),
            'union_id' => $this->integer()->notNull(),
            'course' => $this->integer(),
            'subject_cat_id' => $this->integer()->notNull(),
            'subject_cat_id' => $this->integer(),
            'subject_id' => $this->integer(),
            'subject_type_id' => $this->integer(),
            'subject_vid_id' => $this->integer(),
            'sect_name' => $this->string(64),
            'studyplan_list' => $this->text(),
            'week_time' => $this->float(),
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

    }

    public function down()
    {
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
        $this->dropForeignKey('subject_sect_ibfk_1', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_2', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_3', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_4', 'subject_sect');
        $this->dropForeignKey('subject_sect_ibfk_5', 'subject_sect');
        $this->dropTableWithHistory('teachers_plan');
        $this->dropTableWithHistory('sect_schedule');
        $this->dropTableWithHistory('teachers_load');
        $this->dropTableWithHistory('subject_sect');
        $this->dropTableWithHistory('education_union');
    }
}
