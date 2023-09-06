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
            'id' => $this->primaryKey(),
            'subject_sect_studyplan_id' => $this->integer()->defaultValue(0),
            'studyplan_subject_id' => $this->integer()->defaultValue(0),
            'direction_id' => $this->integer()->notNull(),
            'direction_vid_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'load_time' => $this->float()->notNull(),
            'load_time_consult' => $this->float()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_load', 'Нагрузка преподавателя');
        $this->createIndex('subject_sect_studyplan_id', 'teachers_load', 'subject_sect_studyplan_id');
        $this->createIndex('teachers_id', 'teachers_load', 'teachers_id');
        $this->addForeignKey('teachers_load_ibfk_1', 'teachers_load', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_load_ibfk_2', 'teachers_load', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_load_ibfk_3', 'teachers_load', 'direction_vid_id', 'guide_teachers_direction_vid', 'id', 'NO ACTION', 'NO ACTION');
//        $this->addForeignKey('teachers_load_ibfk_3', 'teachers_load', 'subject_sect_studyplan_id', 'subject_sect_studyplan', 'id', 'CASCADE', 'CASCADE');
//        $this->addForeignKey('teachers_load_ibfk_4', 'teachers_load', 'studyplan_subject_id', 'studyplan_subject', 'id', 'CASCADE', 'CASCADE');


        $this->db->createCommand()->createView('teachers_load_studyplan_view', '
       SELECT studyplan_subject.id AS studyplan_subject_id,
    studyplan_subject.week_time,
    studyplan_subject.year_time_consult,
    0 AS subject_sect_studyplan_id,
    studyplan_subject.id::text AS studyplan_subject_list,
    0 AS subject_sect_id,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
    studyplan.plan_year,
    studyplan.status,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    teachers_load.load_time,
    teachers_load.load_time_consult,
    guide_subject_category.sort_order,
    studyplan_subject.subject_vid_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_id,
    \'Индивидуально\'::text AS sect_name,
    subject.name AS subject_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject
   FROM studyplan
     JOIN studyplan_subject ON studyplan_subject.studyplan_id = studyplan.id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
UNION ALL
 SELECT studyplan_subject.id AS studyplan_subject_id,
    studyplan_subject.week_time,
    studyplan_subject.year_time_consult,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    subject_sect_studyplan.studyplan_subject_list,
    subject_sect.id AS subject_sect_id,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
    studyplan.plan_year,
    studyplan.status,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    teachers_load.load_time,
    teachers_load.load_time_consult,
    guide_subject_category.sort_order,
    studyplan_subject.subject_vid_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_id,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name,
    subject.name AS subject_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \') AS subject
   FROM studyplan
     JOIN studyplan_subject ON studyplan.id = studyplan_subject.studyplan_id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
     JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
     LEFT JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
UNION ALL
 SELECT studyplan_subject.id AS studyplan_subject_id,
    studyplan_subject.week_time,
    studyplan_subject.year_time_consult,
    NULL::integer AS subject_sect_studyplan_id,
    NULL::text AS studyplan_subject_list,
    NULL::integer AS subject_sect_id,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
    NULL::text AS student_fio,
    studyplan.plan_year,
    studyplan.status,
    NULL::integer AS teachers_load_id,
    NULL::integer AS direction_id,
    NULL::integer AS teachers_id,
    NULL::double precision AS load_time,
    NULL::double precision AS load_time_consult,
    guide_subject_category.sort_order,
    studyplan_subject.subject_vid_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_id,
    NULL::text AS sect_name,
    subject.name AS subject_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \') AS subject
   FROM studyplan_subject
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
  WHERE NOT (studyplan_subject.id IN ( SELECT studyplan_subject_1.id
           FROM studyplan studyplan_1
             JOIN studyplan_subject studyplan_subject_1 ON studyplan_subject_1.studyplan_id = studyplan_1.id
             JOIN guide_subject_vid guide_subject_vid_1 ON guide_subject_vid_1.id = studyplan_subject_1.subject_vid_id AND guide_subject_vid_1.qty_min = 1 AND guide_subject_vid_1.qty_max = 1
             LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject_1.id AND teachers_load.subject_sect_studyplan_id = 0
        UNION ALL
         SELECT studyplan_subject_1.id
           FROM studyplan studyplan_1
             JOIN studyplan_subject studyplan_subject_1 ON studyplan_1.id = studyplan_subject_1.studyplan_id
             JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject_1.subject_cat_id AND subject_sect.subject_id = studyplan_subject_1.subject_id AND subject_sect.subject_vid_id = studyplan_subject_1.subject_vid_id
             JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject_1.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
             LEFT JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0))
  ORDER BY 23, 18, 19, 13, 14;
        ')->execute();

        $this->db->createCommand()->createView('teachers_load_view', '
     SELECT studyplan_subject.id AS studyplan_subject_id,
    0 AS subject_sect_studyplan_id,
    studyplan_subject.id::text AS studyplan_subject_list,
    0 AS subject_sect_id,
    studyplan.plan_year,
    studyplan_subject.week_time,
    studyplan_subject.year_time_consult,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.direction_vid_id,
    teachers_load.teachers_id,
    teachers_load.load_time,
        CASE
            WHEN studyplan_subject.subject_type_id = 1000 THEN teachers_load.load_time
            ELSE 0::double precision
        END AS load_time_0,
        CASE
            WHEN studyplan_subject.subject_type_id = 1001 THEN teachers_load.load_time
            ELSE 0::double precision
        END AS load_time_1,
    teachers_load.load_time_consult,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject,
    studyplan_subject.subject_type_id,
    guide_subject_type.name AS subject_type_name,
    studyplan_subject.subject_id,
    subject.name AS subject_name
   FROM studyplan_subject
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id AND studyplan.status = 1
     LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
UNION ALL
 SELECT 0 AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
        CASE
            WHEN subject_sect_studyplan.studyplan_subject_list = \'\'::text THEN NULL::text
            ELSE subject_sect_studyplan.studyplan_subject_list
        END AS studyplan_subject_list,
    subject_sect.id AS subject_sect_id,
    subject_sect_studyplan.plan_year,
    ( SELECT max(studyplan_subject.week_time) AS max
           FROM studyplan_subject
          WHERE studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[])) AS week_time,
    ( SELECT max(studyplan_subject.year_time_consult) AS max
           FROM studyplan_subject
          WHERE studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[])) AS year_time_consult,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.direction_vid_id,
    teachers_load.teachers_id,
    teachers_load.load_time,
        CASE
            WHEN subject_sect_studyplan.subject_type_id = 1000 THEN teachers_load.load_time
            ELSE 0::double precision
        END AS load_time_0,
        CASE
            WHEN subject_sect_studyplan.subject_type_id = 1001 THEN teachers_load.load_time
            ELSE 0::double precision
        END AS load_time_1,
    teachers_load.load_time_consult,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \') \') AS subject,
    subject_sect_studyplan.subject_type_id,
    guide_subject_type.name AS subject_type_name,
    subject_sect.subject_id,
    subject.name AS subject_name
   FROM subject_sect_studyplan
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     LEFT JOIN teachers_load ON subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id AND teachers_load.studyplan_subject_id = 0
     JOIN subject ON subject.id = subject_sect.subject_id
     JOIN guide_subject_category ON guide_subject_category.id = subject_sect.subject_cat_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = subject_sect_studyplan.subject_type_id
     JOIN guide_subject_vid ON guide_subject_vid.id = subject_sect.subject_vid_id
  ORDER BY 16, 17, 9, 10, 11;
        ')->execute();

        $this->createTableWithHistory('subject_schedule', [
            'id' => $this->primaryKey(),
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
        $this->addForeignKey('subject_schedule_ibfk_1', 'subject_schedule', 'teachers_load_id', 'teachers_load', 'id', 'CASCADE', 'CASCADE');

        $this->createTableWithHistory('teachers_plan', [
            'id' => $this->primaryKey(),
            'direction_id' => $this->integer()->notNull(),
            'teachers_id' => $this->integer()->notNull(),
            'plan_year' => $this->integer(),
            'half_year' => $this->integer()->defaultValue(0),
            'week_num' => $this->integer()->defaultValue(0),
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
        $this->addForeignKey('teachers_plan_ibfk_1', 'teachers_plan', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_plan_ibfk_2', 'teachers_plan', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('consult_schedule', [
            'id' => $this->primaryKey(),
            'teachers_load_id' => $this->integer(),
            'datetime_in' => $this->integer(),
            'datetime_out' => $this->integer(),
            'auditory_id' => $this->integer(),
            'description' => $this->string(512),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('consult_schedule', 'Расписание консультаций');
        $this->addForeignKey('consult_schedule_ibfk_1', 'consult_schedule', 'teachers_load_id', 'teachers_load', 'id', 'CASCADE', 'CASCADE');

        $this->createTableWithHistory('consult_schedule_confirm', [
            'id' => $this->primaryKey(),
            'teachers_id' => $this->integer()->notNull(),
            'plan_year' => $this->integer()->notNull(),
            'confirm_flag' => $this->boolean()->notNull()->defaultValue(false),
            'teachers_sign' => $this->integer(),
            'timestamp_sign' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('consult_schedule_confirm', 'Утверждение расписания консультаций');
        $this->addForeignKey('consult_schedule_confirm_ibfk_1', 'consult_schedule_confirm', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('consult_schedule_confirm_ibfk_2', 'consult_schedule_confirm', 'teachers_sign', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {

        $this->dropForeignKey('consult_schedule_confirm_ibfk_2', 'consult_schedule_confirm');
        $this->dropForeignKey('consult_schedule_confirm_ibfk_1', 'consult_schedule_confirm');
        $this->dropForeignKey('consult_schedule_ibfk_1', 'consult_schedule');
        $this->dropForeignKey('teachers_plan_ibfk_1', 'teachers_plan');
        $this->dropForeignKey('teachers_plan_ibfk_2', 'teachers_plan');
        $this->dropForeignKey('subject_schedule_ibfk_1', 'subject_schedule');
        $this->db->createCommand()->dropView('teachers_load_view')->execute();
        $this->db->createCommand()->dropView('teachers_load_studyplan_view')->execute();
        $this->dropForeignKey('teachers_load_ibfk_1', 'teachers_load');
        $this->dropForeignKey('teachers_load_ibfk_2', 'teachers_load');
        $this->dropTableWithHistory('consult_schedule_confirm');
        $this->dropTableWithHistory('consult_schedule');
        $this->dropTableWithHistory('teachers_plan');
        $this->dropTableWithHistory('subject_schedule');
        $this->dropTableWithHistory('teachers_load');

    }
}
