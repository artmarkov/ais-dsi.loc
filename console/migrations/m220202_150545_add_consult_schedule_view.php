<?php


class m220202_150545_add_consult_schedule_view extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->db->createCommand()->createView('consult_schedule_teachers_view', '
        (select teachers_load.id as teachers_load_id,
				teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
				teachers_load.studyplan_subject_id as studyplan_subject_id,
                teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id,
				studyplan.id::text as studyplan_subject_list,
				studyplan.course as course,
			    studyplan_subject.subject_cat_id as subject_cat_id,
			    studyplan_subject.subject_id as subject_id,
			    studyplan_subject.subject_type_id as subject_type_id,
			    studyplan_subject.subject_vid_id as subject_vid_id,
				studyplan_subject.year_time_consult as year_time_consult,
				studyplan.plan_year as plan_year,
				consult_schedule.id as consult_schedule_id,
			    consult_schedule.datetime_in as datetime_in,
			    consult_schedule.datetime_out as datetime_out,
			    consult_schedule.auditory_id as auditory_id,
			    consult_schedule.description as description
                 from teachers_load
				 inner join studyplan_subject on (studyplan_subject.id = teachers_load.studyplan_subject_id and teachers_load.subject_sect_studyplan_id = 0 and studyplan_subject.year_time_consult > 0)
				 inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
				 left join consult_schedule  on (consult_schedule.teachers_load_id = teachers_load.id)
				 )
 UNION ALL 
	    (select teachers_load.id as teachers_load_id,
  				teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
  				teachers_load.studyplan_subject_id as studyplan_subject_id,
                teachers_load.direction_id as direction_id,
                teachers_load.teachers_id as teachers_id,
				subject_sect_studyplan.studyplan_subject_list as studyplan_subject_list,
				subject_sect.course as course,
			    subject_sect.subject_cat_id as subject_cat_id,
			    subject_sect.subject_id as subject_id,
			    subject_sect.subject_type_id as subject_type_id,
			    subject_sect.subject_vid_id as subject_vid_id,
			    (SELECT MAX(year_time_consult)
	                FROM studyplan_subject 
	                where studyplan_subject.year_time_consult > 0 
	                and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) as year_time_consult,
				subject_sect.plan_year as plan_year,
				consult_schedule.id as consult_schedule_id,
			    consult_schedule.datetime_in as datetime_in,
			    consult_schedule.datetime_out as datetime_out,
			    consult_schedule.auditory_id as auditory_id,
			    consult_schedule.description as description
                 from teachers_load
				 inner join subject_sect_studyplan on (subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id and teachers_load.studyplan_subject_id = 0)
				 inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
				 left join consult_schedule  on (consult_schedule.teachers_load_id = teachers_load.id)
				 )
ORDER BY direction_id, teachers_id, datetime_in
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('consult_schedule_teachers_view')->execute();
    }
}
