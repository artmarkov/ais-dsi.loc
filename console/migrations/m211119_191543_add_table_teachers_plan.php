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

    }

    public function down()
    {

        $this->dropForeignKey('teachers_plan_ibfk_1', 'teachers_plan');
        $this->dropForeignKey('teachers_plan_ibfk_2', 'teachers_plan');
        $this->dropForeignKey('sect_schedule_ibfk_1', 'sect_schedule');
        $this->dropForeignKey('sect_schedule_ibfk_2', 'sect_schedule');
        $this->dropForeignKey('sect_schedule_ibfk_3', 'sect_schedule');
        $this->dropForeignKey('teachers_load_ibfk_1', 'teachers_load');
        $this->dropForeignKey('teachers_load_ibfk_2', 'teachers_load');
        $this->dropForeignKey('teachers_load_ibfk_3', 'teachers_load');

        $this->dropTableWithHistory('teachers_plan');
        $this->dropTableWithHistory('sect_schedule');
        $this->dropTableWithHistory('teachers_load');

    }
}
