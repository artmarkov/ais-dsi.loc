<?php


class m220214_204015_ref extends \artsoft\db\BaseMigration
{

    public function up()
    {


        $this->db->createCommand()->createView('consult_schedule_view', '
(select studyplan_subject.id as studyplan_subject_id,
                 0 as subject_sect_studyplan_id,
                 studyplan_subject.id::text as studyplan_subject_list,
                 0 as subject_sect_id,
                 studyplan.plan_year as plan_year,
                 studyplan_subject.year_time_consult as year_time_consult,
                 teachers_load.id as teachers_load_id,                         
                 teachers_load.direction_id as direction_id,
                 teachers_load.teachers_id as teachers_id,
                 teachers_load.load_time as load_time,
                 consult_schedule.id as consult_schedule_id,
						 consult_schedule.datetime_in as datetime_in,
						 consult_schedule.datetime_out as datetime_out,
						 consult_schedule.auditory_id as auditory_id,
						 consult_schedule.description as description
             from studyplan_subject
			 inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
			 inner join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id 
			 and teachers_load.subject_sect_studyplan_id = 0)
			 left join consult_schedule  on (consult_schedule.teachers_load_id = teachers_load.id)           
           )
UNION ALL
         (select 0 as studyplan_subject_id,
                 subject_sect_studyplan.id as subject_sect_studyplan_id,
                 subject_sect_studyplan.studyplan_subject_list as studyplan_subject_list,
                 subject_sect.id as subject_sect_id,
                 subject_sect.plan_year as plan_year,
                 (select MAX(year_time_consult) 
					 from studyplan_subject 
					 where studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])
				 ) as year_time_consult,
                 teachers_load.id as teachers_load_id,                         
                 teachers_load.direction_id as direction_id,
                 teachers_load.teachers_id as teachers_id,
                 teachers_load.load_time as load_time,
                 consult_schedule.id as consult_schedule_id,
						 consult_schedule.datetime_in as datetime_in,
						 consult_schedule.datetime_out as datetime_out,
						 consult_schedule.auditory_id as auditory_id,
						 consult_schedule.description as description
             from subject_sect_studyplan
			 inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
			 inner join teachers_load on (subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id 
			  and teachers_load.studyplan_subject_id = 0)
			 left join consult_schedule  on (consult_schedule.teachers_load_id = teachers_load.id)
			  )
ORDER BY  subject_sect_studyplan_id, studyplan_subject_id, direction_id, teachers_id, datetime_in
        ')->execute();

        $this->db->createCommand()->createView('consult_schedule_studyplan_view', '
(select studyplan_subject.id AS studyplan_subject_id,
                    studyplan_subject.year_time_consult as year_time_consult,
                    0 AS subject_sect_studyplan_id,
                    studyplan_subject.id::text AS studyplan_subject_list,
                    0 AS subject_sect_id,
                    studyplan.id AS studyplan_id,
                    studyplan.student_id,
                    studyplan.plan_year,
                    studyplan.status,
                    teachers_load.id AS teachers_load_id,
                    teachers_load.direction_id,
                    teachers_load.teachers_id,
                    consult_schedule.id as consult_schedule_id,
					consult_schedule.datetime_in as datetime_in,
					consult_schedule.datetime_out as datetime_out,
					consult_schedule.auditory_id as auditory_id,
					consult_schedule.description as description
                 from studyplan_subject
                 inner join studyplan ON studyplan_subject.studyplan_id = studyplan.id
                 inner join guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
                 inner join teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
                 left join consult_schedule  on (consult_schedule.teachers_load_id = teachers_load.id)
           )
UNION ALL
 (select studyplan_subject.id AS studyplan_subject_id,
                    (select MAX(year_time_consult)
	                	from studyplan_subject 
	                	where studyplan_subject.year_time_consult > 0 
	                	and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) as year_time_consult,
                    subject_sect_studyplan.id AS subject_sect_studyplan_id,
                    subject_sect_studyplan.studyplan_subject_list,
                    subject_sect.id AS subject_sect_id,
                    studyplan.id AS studyplan_id,
                    studyplan.student_id,
                    studyplan.plan_year,
                    studyplan.status,
                    teachers_load.id AS teachers_load_id,
                    teachers_load.direction_id,
                    teachers_load.teachers_id,
                    consult_schedule.id as consult_schedule_id,
				    consult_schedule.datetime_in as datetime_in,
				    consult_schedule.datetime_out as datetime_out,
				    consult_schedule.auditory_id as auditory_id,
				    consult_schedule.description as description
                 from studyplan_subject
                 inner join studyplan ON studyplan.id = studyplan_subject.studyplan_id
                 left join subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                 inner join subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                 inner join teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                 left join consult_schedule  on (consult_schedule.teachers_load_id = teachers_load.id)
   )
ORDER BY subject_sect_studyplan_id, studyplan_subject_id, direction_id, teachers_id, datetime_in
        ')->execute();
    }



    public function down()
    {

        $this->db->createCommand()->dropView('consult_schedule_studyplan_view')->execute();
        $this->db->createCommand()->dropView('consult_schedule_view')->execute();
    }
}
