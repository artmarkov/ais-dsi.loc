<?php


class m211119_191543_add_table_teachers_plan extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('teachers_load', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'subject_sect_studyplan_id' => $this->integer()->defaultValue(0),
            'studyplan_subject_id' => $this->integer()->defaultValue(0),
            'direction_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'week_time' => $this->float()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_load', 'Нагрузка преподавателя');
        $this->db->createCommand()->resetSequence('teachers_load', 10000)->execute();
        $this->createIndex('subject_sect_studyplan_id', 'teachers_load', 'subject_sect_studyplan_id');
        $this->createIndex('teachers_id', 'teachers_load', 'teachers_id');
        $this->addForeignKey('teachers_load_ibfk_1', 'teachers_load', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_load_ibfk_2', 'teachers_load', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->createView('teachers_load_view', '
         SELECT teachers_load.id as id, teachers.id AS teachers_id, 
                teachers_load.week_time as week_time,
                CONCAT(user_common.last_name ,\' \', left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'.(\', guide_teachers_direction.slug, \') \', teachers_load.week_time) as teachers_load_display,
                user_common.status
        FROM teachers_load
		INNER JOIN teachers ON teachers_load.teachers_id = teachers.id
		INNER JOIN guide_teachers_direction ON guide_teachers_direction.id = teachers_load.direction_id
        INNER JOIN user_common ON user_common.id = teachers.user_common_id 
        WHERE user_common.user_category=\'teachers\'
        ORDER BY user_common.last_name, user_common.first_name
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_load_display', 'teachers_load_view', 'id', 'teachers_load_display', 'status', null, null, 'Нагрузка преподавателей(с ФИО и видом деятельности)'],
        ])->execute();

        $this->createTableWithHistory('subject_schedule', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'teachers_load_id' => $this->integer(),
            'week_num' => $this->integer(),
            'week_day' => $this->integer(),
            'time_in' => $this->integer(),
            'time_out' => $this->integer(),
            'auditory_id' => $this->integer(),
            'description' => $this->string(512),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject_schedule', 'Расписание занятий');
        $this->db->createCommand()->resetSequence('subject_schedule', 10000)->execute();
        $this->addForeignKey('subject_schedule_ibfk_1', 'subject_schedule', 'teachers_load_id', 'teachers_load', 'id', 'CASCADE', 'CASCADE');

        $this->createTableWithHistory('teachers_plan', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'direction_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'plan_year' => $this->integer(),
            'week_num' => $this->integer(),
            'week_day' => $this->integer(),
            'time_plan_in' => $this->integer(),
            'time_plan_out' => $this->integer(),
            'auditory_id' => $this->integer(),
            'description' => $this->string(512),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_plan', 'Планирование инд. занятий преподавателя');
        $this->db->createCommand()->resetSequence('teachers_plan', 10000)->execute();
        $this->addForeignKey('teachers_plan_ibfk_1', 'teachers_plan', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_plan_ibfk_2', 'teachers_plan', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {

        $this->dropForeignKey('teachers_plan_ibfk_1', 'teachers_plan');
        $this->dropForeignKey('teachers_plan_ibfk_2', 'teachers_plan');
        $this->dropForeignKey('subject_schedule_ibfk_1', 'subject_schedule');
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_load_display'])->execute();
        $this->db->createCommand()->dropView('teachers_load_view')->execute();
        $this->dropForeignKey('teachers_load_ibfk_1', 'teachers_load');
        $this->dropForeignKey('teachers_load_ibfk_2', 'teachers_load');

        $this->dropTableWithHistory('teachers_plan');
        $this->dropTableWithHistory('subject_schedule');
        $this->dropTableWithHistory('teachers_load');

    }
}
