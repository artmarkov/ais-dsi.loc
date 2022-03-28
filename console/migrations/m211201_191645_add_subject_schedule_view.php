<?php


class m211201_191645_add_subject_schedule_view extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $this->db->createCommand()->createView('subject_schedule_view', '
        (select studyplan_subject.id as studyplan_subject_id,
                 0 as subject_sect_studyplan_id,
                 studyplan_subject.id::text as studyplan_subject_list,
                 0 as subject_sect_id,
                 studyplan.plan_year as plan_year,
                 studyplan_subject.week_time as week_time,
                 teachers_load.id as teachers_load_id,                         
                 teachers_load.direction_id as direction_id,
                 teachers_load.teachers_id as teachers_id,
                 teachers_load.load_time as load_time,
                 subject_schedule.id as subject_schedule_id,
                 subject_schedule.week_num as week_num,
                 subject_schedule.week_day as week_day,
                 subject_schedule.time_in as time_in,
                 subject_schedule.time_out as time_out,
                 subject_schedule.auditory_id as auditory_id,
                 subject_schedule.description as description
             from studyplan_subject
			 inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
			 inner join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id 
			 and teachers_load.subject_sect_studyplan_id = 0)
			 left join subject_schedule  on (subject_schedule.teachers_load_id = teachers_load.id)
           )
UNION ALL
         (select 0 as studyplan_subject_id,
                 subject_sect_studyplan.id as subject_sect_studyplan_id,
                 subject_sect_studyplan.studyplan_subject_list as studyplan_subject_list,
                 subject_sect.id as subject_sect_id,
                 subject_sect.plan_year as plan_year,
                 (select MAX(week_time) 
					 from studyplan_subject 
					 where studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])
				 ) as week_time,
                 teachers_load.id as teachers_load_id,                         
                 teachers_load.direction_id as direction_id,
                 teachers_load.teachers_id as teachers_id,
                 teachers_load.load_time as load_time,
                 subject_schedule.id as subject_schedule_id,
                 subject_schedule.week_num as week_num,
                 subject_schedule.week_day as week_day,
                 subject_schedule.time_in as time_in,
                 subject_schedule.time_out as time_out,
                 subject_schedule.auditory_id as auditory_id,
                 subject_schedule.description as description
             from subject_sect_studyplan
			 inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
			 inner join teachers_load on (subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id 
			  and teachers_load.studyplan_subject_id = 0)
			 left join subject_schedule  on (subject_schedule.teachers_load_id = teachers_load.id)
			  )
ORDER BY  subject_sect_studyplan_id, studyplan_subject_id, direction_id, teachers_id, week_day, time_in
        ')->execute();


        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_schedule_plan_year', 'subject_schedule_view', 'subject_schedule_id', 'plan_year', 'plan_year', null, null, 'Получение года обучения по ИД расписания группы'],
        ])->execute();

        $this->db->createCommand()->createView('subject_schedule_studyplan_view', '
           (select studyplan.id as studyplan_id,
                         studyplan.student_id as student_id,
                         studyplan.plan_year as plan_year,
                         studyplan.programm_id as programm_id,
                         studyplan.speciality_id as speciality_id,
                         studyplan.course as course,
                         studyplan.status as status,
                         studyplan_subject.id as studyplan_subject_id,
                         studyplan_subject.subject_cat_id as subject_cat_id,
                         studyplan_subject.subject_id as subject_id,
                         studyplan_subject.subject_type_id as subject_type_id,
                         studyplan_subject.subject_vid_id as subject_vid_id,
                         studyplan_subject.week_time as week_time,
                         studyplan_subject.year_time as year_time,
                         teachers_load.id as teachers_load_id,
                         teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
                         teachers_load.direction_id as direction_id,
                         teachers_load.teachers_id as teachers_id,
                         teachers_load.load_time as load_time,
                         subject_schedule.id as subject_schedule_id,
                         subject_schedule.week_num as week_num,
                         subject_schedule.week_day as week_day,
                         subject_schedule.time_in as time_in,
                         subject_schedule.time_out as time_out,
                         subject_schedule.auditory_id as auditory_id,
                         subject_schedule.description as description
                 from studyplan
                 inner join studyplan_subject on (studyplan.id = studyplan_subject.studyplan_id)
                 inner join guide_subject_vid on (guide_subject_vid.id = studyplan_subject.subject_vid_id and guide_subject_vid.qty_min = 1 and guide_subject_vid.qty_max = 1)
                 left join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id 
											and teachers_load.subject_sect_studyplan_id = 0)
                 left join subject_schedule  on (subject_schedule.teachers_load_id = teachers_load.id)
           )
           UNION
           (select studyplan.id as studyplan_id,
                         studyplan.student_id as student_id,
                         studyplan.plan_year as plan_year,
                         studyplan.programm_id as programm_id,
                         studyplan.speciality_id as speciality_id,
                         studyplan.course as course,
                         studyplan.status as status,
                         studyplan_subject.id as studyplan_subject_id,
                         studyplan_subject.subject_cat_id as subject_cat_id,
                         studyplan_subject.subject_id as subject_id,
                         studyplan_subject.subject_type_id as subject_type_id,
                         studyplan_subject.subject_vid_id as subject_vid_id,
                         studyplan_subject.week_time as week_time,
                         studyplan_subject.year_time as year_time,
                         teachers_load.id as teachers_load_id,
                         teachers_load.subject_sect_studyplan_id as subject_sect_studyplan_id,
                         teachers_load.direction_id as direction_id,
                         teachers_load.teachers_id as teachers_id,
                         teachers_load.load_time as load_time,
                         subject_schedule.id as subject_schedule_id,
                         subject_schedule.week_num as week_num,
                         subject_schedule.week_day as week_day,
                         subject_schedule.time_in as time_in,
                         subject_schedule.time_out as time_out,
                         subject_schedule.auditory_id as auditory_id,
                         subject_schedule.description as description		
                 from studyplan
                 inner join studyplan_subject on (studyplan_subject.studyplan_id = studyplan.id)
                 left join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
                                           and subject_sect.subject_id = studyplan_subject.subject_id
                                           and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
                 inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 				   
                 left join teachers_load  on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
		                            and teachers_load.studyplan_subject_id = 0)
                 left join subject_schedule  on (subject_schedule.teachers_load_id = teachers_load.id)							
           )
           ORDER BY studyplan_id, subject_cat_id, subject_sect_studyplan_id, direction_id, week_day, time_in
  		   
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('subject_schedule_studyplan_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_schedule_plan_year'])->execute();
        $this->db->createCommand()->dropView('subject_schedule_view')->execute();
    }
}
