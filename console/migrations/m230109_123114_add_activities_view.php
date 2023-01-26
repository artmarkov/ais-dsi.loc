<?php

use \artsoft\db\BaseMigration;

class m230109_123114_add_activities_view extends BaseMigration
{
    public function up()
    {
        $this->db->createCommand()->createView('generator_date_view', '
    SELECT dt.dt::date AS date,
    date_part(\'epoch\'::text, dt.dt::date) AS "timestamp",
    date_part(\'year\'::text, dt.dt::date) AS year,
    date_part(\'month\'::text, dt.dt::date) AS month,
    date_part(\'day\'::text, dt.dt::date) AS day,
    ((date_part(\'day\'::text, dt.dt::date) + date_part(\'dow\'::text, format(\'%s-%s-%s\'::text, date_part(\'year\'::text, dt.dt::date), date_part(\'month\'::text, dt.dt::date), 1)::date) - 5::double precision) / 7::double precision + 1::double precision)::integer AS week_num,
        CASE
            WHEN date_part(\'dow\'::text, dt.dt::date) = 0::double precision THEN 7::double precision
            ELSE date_part(\'dow\'::text, dt.dt::date)
        END AS week_day
   FROM generate_series(to_char(( SELECT CURRENT_TIMESTAMP - \'1 year\'::interval), \'YYYY-MM-DD\'::text)::date::timestamp with time zone, to_char(( SELECT CURRENT_TIMESTAMP + \'1 year\'::interval), \'YYYY-MM-DD\'::text)::date::timestamp with time zone, \'1 day\'::interval) dt(dt);
        ')->execute();

        $this->db->createCommand()->createView('generator_course_view', '
     SELECT p.term_mastering,
    p.subject_cat_id,
    p.subject_vid_id,
    p.subject_id,
    p.sect_name,
    array_to_string(ARRAY( SELECT DISTINCT education_programm.id
           FROM education_programm
             JOIN education_programm_level ON education_programm_level.programm_id = education_programm.id
             JOIN education_programm_level_subject ON education_programm_level_subject.programm_level_id = education_programm_level.id
          WHERE education_programm.term_mastering = p.term_mastering AND education_programm_level_subject.subject_cat_id = p.subject_cat_id AND education_programm_level_subject.subject_vid_id = p.subject_vid_id AND education_programm_level_subject.subject_id = p.subject_id
          ORDER BY education_programm.id), \',\'::text) AS programm_list,
    array_to_string(ARRAY( SELECT DISTINCT education_programm_level.course
           FROM education_programm
             JOIN education_programm_level ON education_programm_level.programm_id = education_programm.id
             JOIN education_programm_level_subject ON education_programm_level_subject.programm_level_id = education_programm_level.id
          WHERE education_programm.term_mastering = p.term_mastering AND education_programm_level_subject.subject_cat_id = p.subject_cat_id AND education_programm_level_subject.subject_vid_id = p.subject_vid_id AND education_programm_level_subject.subject_id = p.subject_id
          ORDER BY education_programm_level.course), \',\'::text) AS course_list,
        CASE
            WHEN p.subject_vid_id = 1001 THEN 1
            ELSE 0
        END AS course_flag
   FROM ( SELECT education_programm.term_mastering,
            education_programm_level_subject.subject_cat_id,
            education_programm_level_subject.subject_vid_id,
            education_programm_level_subject.subject_id,
            subject.name AS sect_name
           FROM education_programm
             JOIN education_programm_level ON education_programm_level.programm_id = education_programm.id
             JOIN education_programm_level_subject ON education_programm_level_subject.programm_level_id = education_programm_level.id
             JOIN subject ON subject.id = education_programm_level_subject.subject_id
          WHERE education_programm_level_subject.subject_id IS NOT NULL AND education_programm_level_subject.subject_vid_id <> 1000
          GROUP BY education_programm.term_mastering, education_programm_level_subject.subject_cat_id, education_programm_level_subject.subject_vid_id, education_programm_level_subject.subject_id, subject.name
          ORDER BY subject.name) p;
        ')->execute();

        $this->db->createCommand()->createView('activities_schedule_view', '
SELECT data.subject_schedule_id,
	data.direction_id,
    data.teachers_id,
    data.title,
    data.auditory_id,
    data.description,
    data."timestamp" + data.time_in::double precision AS datetime_in,
    data."timestamp" + data.time_out::double precision AS datetime_out
   FROM ( SELECT gen."timestamp",
            subject_schedule_view.subject_schedule_id,
            subject_schedule_view.time_in,
            subject_schedule_view.time_out,
            subject_schedule_view.auditory_id,
            subject_schedule_view.description,
            subject_schedule_view.direction_id,
		    subject_schedule_view.teachers_id,
            subject_schedule_view.plan_year,
            concat(\'Занятие: \',
                CASE
                    WHEN subject_schedule_view.studyplan_subject_id = 0 AND subject_schedule_view.subject_sect_studyplan_id <> 0 THEN ( SELECT studyplan_subject_view.memo_4
                       FROM studyplan_subject_view
                      WHERE studyplan_subject_view.subject_sect_studyplan_id = subject_schedule_view.subject_sect_studyplan_id)
                    WHEN subject_schedule_view.studyplan_subject_id <> 0 AND subject_schedule_view.subject_sect_studyplan_id = 0 THEN ( SELECT studyplan_subject_view.memo_4
                       FROM studyplan_subject_view
                      WHERE studyplan_subject_view.studyplan_subject_id = subject_schedule_view.studyplan_subject_id)
                    ELSE NULL::text
                END) AS title
           FROM generator_date_view gen
             JOIN subject_schedule_view ON gen.week_day = subject_schedule_view.week_day::double precision AND
                CASE
                    WHEN subject_schedule_view.week_num IS NOT NULL THEN subject_schedule_view.week_num = gen.week_num
                    ELSE true
                END) data
  WHERE data."timestamp" >= date_part(\'epoch\'::text, format(\'%s-%s-%s\'::text, data.plan_year, 9, 1)::date) AND data."timestamp" <= date_part(\'epoch\'::text, format(\'%s-%s-%s\'::text, data.plan_year + 1, 5, 31)::date);       ')->execute();


        $this->db->createCommand()->createView('activities_view', '
 SELECT \'schoolplan\'::text AS resource,
    schoolplan.id,
    1000 AS category_id,
    schoolplan.auditory_id,
    schoolplan.executors_list,
    schoolplan.title,
    schoolplan.description,
    schoolplan.datetime_in AS start_time,
    schoolplan.datetime_out AS end_time
   FROM schoolplan
   WHERE schoolplan.auditory_id IS NOT NULL
UNION ALL
 SELECT \'consult_schedule\'::text AS resource,
    consult_schedule.id,
    1002 AS category_id,
    consult_schedule.auditory_id,
    teachers_load.teachers_id::text AS executors_list,
    concat(\'Консультация: \',
        CASE
            WHEN teachers_load.studyplan_subject_id = 0 AND teachers_load.subject_sect_studyplan_id <> 0 THEN ( SELECT studyplan_subject_view.memo_4
               FROM studyplan_subject_view
              WHERE studyplan_subject_view.subject_sect_studyplan_id = teachers_load.subject_sect_studyplan_id)
            WHEN teachers_load.studyplan_subject_id <> 0 AND teachers_load.subject_sect_studyplan_id = 0 THEN ( SELECT studyplan_subject_view.memo_4
               FROM studyplan_subject_view
              WHERE studyplan_subject_view.studyplan_subject_id = teachers_load.studyplan_subject_id)
            ELSE NULL::text
        END) AS title,
    consult_schedule.description,
    consult_schedule.datetime_in AS start_time,
    consult_schedule.datetime_out AS end_time
   FROM consult_schedule
     JOIN teachers_load ON teachers_load.id = consult_schedule.teachers_load_id
  WHERE consult_schedule.auditory_id IS NOT NULL AND teachers_load.direction_id = 1000
UNION ALL
 SELECT \'activities_over\'::text AS resource,
    activities_over.id,
    1003 AS category_id,
    activities_over.auditory_id,
    activities_over.executors_list,
    activities_over.title,
    activities_over.description,
    activities_over.datetime_in AS start_time,
    activities_over.datetime_out AS end_time
   FROM activities_over
  WHERE activities_over.auditory_id IS NOT NULL AND activities_over.over_category = 2
UNION ALL
 SELECT \'subject_schedule\'::text AS resource,
    activities_schedule_view.subject_schedule_id AS id,
    1001 AS category_id,
    activities_schedule_view.auditory_id,
    activities_schedule_view.teachers_id::text AS executors_list,
    activities_schedule_view.title,
    activities_schedule_view.description,
    activities_schedule_view.datetime_in AS start_time,
    activities_schedule_view.datetime_out AS end_time
   FROM activities_schedule_view WHERE activities_schedule_view.direction_id = 1000
  ORDER BY 8;
        ')->execute();

    }

    public function down()
    {
        $this->db->createCommand()->dropView('activities_view')->execute();
        $this->db->createCommand()->dropView('activities_schedule_view')->execute();
        $this->db->createCommand()->dropView('generator_course_view')->execute();
        $this->db->createCommand()->dropView('generator_date_view')->execute();
    }
}
