<?php


class m211201_191645_add_subject_schedule_view extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $this->db->createCommand()->createView('subject_schedule_view', '
   SELECT studyplan_subject.id AS studyplan_subject_id,
    0 AS subject_sect_studyplan_id,
    studyplan_subject.id::text AS studyplan_subject_list,
    studyplan_subject.subject_type_id,
    0 AS subject_sect_id,
    studyplan.plan_year,
    studyplan_subject.week_time,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.direction_vid_id,
    teachers_load.teachers_id,
    teachers_load.load_time,
    subject_schedule.id AS subject_schedule_id,
    subject_schedule.week_num,
    subject_schedule.week_day,
    subject_schedule.time_in,
    subject_schedule.time_out,
    subject_schedule.auditory_id,
    subject_schedule.description,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name, \' (\', studyplan.course, \'/\', education_programm.term_mastering, \' \', education_programm.short_name, \')\') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject,
    studyplan_subject.subject_vid_id,
    studyplan.status,
    studyplan.status_reason,
    studyplan.id AS studyplan_id,
    concat(studyplan_subject.subject_id, \'|\', studyplan_subject.subject_vid_id, \'|\', studyplan_subject.subject_type_id, \'|\', education_programm.education_cat_id) AS subject_key,
    studyplan.programm_id::text AS programm_list
   FROM studyplan_subject
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     LEFT JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     LEFT JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
UNION ALL
 SELECT 0 AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    subject_sect_studyplan.studyplan_subject_list,
    subject_sect_studyplan.subject_type_id,
    subject_sect.id AS subject_sect_id,
    subject_sect_studyplan.plan_year,
    ( SELECT max(studyplan_subject.week_time) AS max
           FROM studyplan_subject
          WHERE studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[])) AS week_time,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.direction_vid_id,
    teachers_load.teachers_id,
    teachers_load.load_time,
    subject_schedule.id AS subject_schedule_id,
    subject_schedule.week_num,
    subject_schedule.week_day,
    subject_schedule.time_in,
    subject_schedule.time_out,
    subject_schedule.auditory_id,
    subject_schedule.description,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \' \', guide_subject_type.slug, \') \') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \') \') AS subject,
    subject_sect.subject_vid_id,
    subject_sect.status,
    0 AS status_reason,
    0 AS studyplan_id,
    NULL::text AS subject_key,
    subject_sect.programm_list
   FROM subject_sect_studyplan
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     JOIN teachers_load ON subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id AND teachers_load.studyplan_subject_id = 0
     JOIN subject ON subject.id = subject_sect.subject_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = subject_sect_studyplan.subject_type_id
     LEFT JOIN guide_subject_vid ON guide_subject_vid.id = subject_sect.subject_vid_id
     LEFT JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
  ORDER BY 20, 21, 9, 11, 14, 15;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('subject_schedule_studyplan_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_schedule_plan_year'])->execute();
        $this->db->createCommand()->dropView('subject_schedule_view')->execute();
    }
}
