<?php


class m211201_191645_add_subject_schedule_view extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $this->db->createCommand()->createView('subject_schedule_view', '
        (select studyplan_subject.id as studyplan_subject_id,
                 0 as subject_sect_studyplan_id,
                 studyplan_subject.id::text as studyplan_subject_list,
                 studyplan_subject.subject_type_id as subject_type_id,
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
                 subject_sect_studyplan.subject_type_id as subject_type_id,
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
           (select studyplan_subject.id AS studyplan_subject_id,
                    studyplan_subject.week_time,
                    0 AS subject_sect_studyplan_id,
                    studyplan_subject.id::text AS studyplan_subject_list,
                    studyplan_subject.subject_type_id as subject_type_id,
                    0 AS subject_sect_id,
                    studyplan.id AS studyplan_id,
                    studyplan.student_id,
                    studyplan.plan_year,
                    studyplan.status,
                    teachers_load.id AS teachers_load_id,
                    teachers_load.direction_id,
                    teachers_load.teachers_id,
                    teachers_load.load_time,
                    subject_schedule.id AS subject_schedule_id,
                    subject_schedule.week_num,
                    subject_schedule.week_day,
                    subject_schedule.time_in,
                    subject_schedule.time_out,
                    subject_schedule.auditory_id,
                    subject_schedule.description
                 from studyplan_subject
                 inner join studyplan ON studyplan_subject.studyplan_id = studyplan.id
                 inner join guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
                 inner join teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
                 left join subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id

           )
UNION ALL
           (select studyplan_subject.id AS studyplan_subject_id,
                    studyplan_subject.week_time,
                    subject_sect_studyplan.id AS subject_sect_studyplan_id,
                    subject_sect_studyplan.studyplan_subject_list,
                    studyplan_subject.subject_type_id as subject_type_id,
                    subject_sect.id AS subject_sect_id,
                    studyplan.id AS studyplan_id,
                    studyplan.student_id,
                    studyplan.plan_year,
                    studyplan.status,
                    teachers_load.id AS teachers_load_id,
                    teachers_load.direction_id,
                    teachers_load.teachers_id,
                    teachers_load.load_time,
                    subject_schedule.id AS subject_schedule_id,
                    subject_schedule.week_num,
                    subject_schedule.week_day,
                    subject_schedule.time_in,
                    subject_schedule.time_out,
                    subject_schedule.auditory_id,
                    subject_schedule.description
                 from studyplan_subject
                 inner join studyplan ON studyplan.id = studyplan_subject.studyplan_id
                 left join subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                 inner join subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                 inner join teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                 left join subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id
   )
ORDER BY subject_sect_studyplan_id, studyplan_subject_id, direction_id, teachers_id, week_day, time_in
  		   
        ')->execute();
    }

    public function down()
    {
        $this->db->createCommand()->dropView('subject_schedule_studyplan_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_schedule_plan_year'])->execute();
        $this->db->createCommand()->dropView('subject_schedule_view')->execute();
    }
}
