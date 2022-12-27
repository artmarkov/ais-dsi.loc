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
    teachers_load.teachers_id,
    teachers_load.id AS teachers_load_id,
    consult_schedule.id AS consult_schedule_id,
    consult_schedule.datetime_in,
    consult_schedule.datetime_out,
    consult_schedule.auditory_id,
    consult_schedule.description
   FROM studyplan_subject
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0 AND teachers_load.load_time_consult > 0::double precision
     LEFT JOIN consult_schedule ON consult_schedule.teachers_load_id = teachers_load.id
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
    teachers_load.teachers_id,
    teachers_load.id AS teachers_load_id,
    consult_schedule.id AS consult_schedule_id,
    consult_schedule.datetime_in,
    consult_schedule.datetime_out,
    consult_schedule.auditory_id,
    consult_schedule.description
   FROM subject_sect_studyplan
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     JOIN teachers_load ON subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id AND teachers_load.studyplan_subject_id = 0 AND teachers_load.load_time_consult > 0::double precision
     LEFT JOIN consult_schedule ON consult_schedule.teachers_load_id = teachers_load.id
  ORDER BY 2, 1, 8, 9, 12;
        ')->execute();

        $this->db->createCommand()->createView('consult_schedule_studyplan_view', ' SELECT studyplan_subject.id AS studyplan_subject_id,
    0 AS subject_sect_studyplan_id,
    studyplan_subject.id::text AS studyplan_subject_list,
    studyplan_subject.subject_type_id,
    studyplan_subject.year_time_consult,
    0 AS subject_sect_id,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
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
    consult_schedule.description
   FROM studyplan_subject
     JOIN studyplan ON studyplan_subject.studyplan_id = studyplan.id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0 AND teachers_load.load_time_consult > 0::double precision
     LEFT JOIN consult_schedule ON consult_schedule.teachers_load_id = teachers_load.id
UNION ALL
 SELECT studyplan_subject.id AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    subject_sect_studyplan.studyplan_subject_list,
    studyplan_subject.subject_type_id,
    subject_sect.id AS year_time_consult,
    studyplan_subject.year_time_consult AS subject_sect_id,
    studyplan.id AS studyplan_id,
    studyplan.student_id,
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
    consult_schedule.description
   FROM studyplan_subject
     JOIN studyplan ON studyplan.id = studyplan_subject.studyplan_id
     LEFT JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
     JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
     JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0 AND teachers_load.load_time_consult > 0::double precision
     LEFT JOIN consult_schedule ON consult_schedule.teachers_load_id = teachers_load.id
  ORDER BY 2, 1, 12, 13, 16;
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('consult_schedule_view')->execute();
        $this->db->createCommand()->dropView('consult_schedule_studyplan_view')->execute();
    }
}
