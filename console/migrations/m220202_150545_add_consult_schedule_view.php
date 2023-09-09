<?php


class m220202_150545_add_consult_schedule_view extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->db->createCommand()->createView('consult_schedule_view', '
   SELECT studyplan_subject.id AS studyplan_subject_id,
    0 AS subject_sect_studyplan_id,
    studyplan_subject.id::text AS studyplan_subject_list,
    studyplan_subject.subject_type_id,
    studyplan_subject.year_time_consult,
    0 AS subject_sect_id,
    studyplan.plan_year,
    teachers_load.load_time_consult,
    teachers_load.direction_id,
	teachers_load.direction_vid_id,
    teachers_load.teachers_id,
    teachers_load.id AS teachers_load_id,
    consult_schedule.id AS consult_schedule_id,
    consult_schedule.datetime_in,
    consult_schedule.datetime_out,
    consult_schedule.auditory_id,
    consult_schedule.description,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject,
    studyplan_subject.subject_vid_id,
    studyplan.status
   FROM studyplan_subject
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0 AND teachers_load.load_time_consult > 0::double precision
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     LEFT JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     LEFT JOIN consult_schedule ON consult_schedule.teachers_load_id = teachers_load.id
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
UNION ALL
 SELECT 0 AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    subject_sect_studyplan.studyplan_subject_list,
    subject_sect_studyplan.subject_type_id,
    ( SELECT max(studyplan_subject.year_time_consult) AS max
           FROM studyplan_subject
          WHERE studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[])) AS year_time_consult,
    subject_sect.id AS subject_sect_id,
    subject_sect_studyplan.plan_year,
    teachers_load.load_time_consult,
    teachers_load.direction_id,
	teachers_load.direction_vid_id,
    teachers_load.teachers_id,
    teachers_load.id AS teachers_load_id,
    consult_schedule.id AS consult_schedule_id,
    consult_schedule.datetime_in,
    consult_schedule.datetime_out,
    consult_schedule.auditory_id,
    consult_schedule.description,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \') AS subject,
    subject_sect.subject_vid_id,
    subject_sect.status
   FROM subject_sect_studyplan
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     JOIN teachers_load ON subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id AND teachers_load.studyplan_subject_id = 0 AND teachers_load.load_time_consult > 0::double precision
     JOIN subject ON subject.id = subject_sect.subject_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = subject_sect.subject_type_id
     LEFT JOIN guide_subject_vid ON guide_subject_vid.id = subject_sect.subject_vid_id
     LEFT JOIN consult_schedule ON consult_schedule.teachers_load_id = teachers_load.id
  ORDER BY 19, 18, 9, 11, 14;
        ')->execute();

        $this->db->createCommand()->createView('consult_schedule_studyplan_view', '
     SELECT studyplan_subject.id AS studyplan_subject_id,
    0 AS subject_sect_studyplan_id,
    studyplan_subject.id::text AS studyplan_subject_list,
    studyplan_subject.subject_type_id,
    studyplan_subject.year_time_consult,
    0 AS subject_sect_id,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
    studyplan.plan_year,
    studyplan.status,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    teachers_load.load_time_consult,
    consult_schedule.id AS consult_schedule_id,
    consult_schedule.datetime_in,
    consult_schedule.datetime_out,
    consult_schedule.auditory_id,
    consult_schedule.description,
    \'Индивидуально\'::text AS sect_name
   FROM studyplan_subject
     JOIN studyplan ON studyplan_subject.studyplan_id = studyplan.id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0 AND teachers_load.load_time_consult > 0::double precision
     LEFT JOIN consult_schedule ON consult_schedule.teachers_load_id = teachers_load.id
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
UNION ALL
 SELECT studyplan_subject.id AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    subject_sect_studyplan.studyplan_subject_list,
    studyplan_subject.subject_type_id,
    subject_sect.id AS year_time_consult,
    studyplan_subject.year_time_consult AS subject_sect_id,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
    studyplan.plan_year,
    studyplan.status,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    teachers_load.load_time_consult,
    consult_schedule.id AS consult_schedule_id,
    consult_schedule.datetime_in,
    consult_schedule.datetime_out,
    consult_schedule.auditory_id,
    consult_schedule.description,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name
   FROM studyplan_subject
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     LEFT JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
     JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
     JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0 AND teachers_load.load_time_consult > 0::double precision
     LEFT JOIN consult_schedule ON consult_schedule.teachers_load_id = teachers_load.id
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('consult_schedule_view')->execute();
        $this->db->createCommand()->dropView('consult_schedule_studyplan_view')->execute();
    }
}
