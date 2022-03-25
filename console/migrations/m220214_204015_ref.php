<?php


class m220214_204015_ref extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $this->db->createCommand()->createView('teachers_load_view', '
         (select studyplan_subject.id as studyplan_subject_id,
			     studyplan_subject.week_time as week_time,
			     0 as subject_sect_studyplan_id,
			     studyplan.id as studyplan_id,
			     studyplan.student_id as student_id,
			     studyplan.plan_year as plan_year,
			     studyplan.status as status,
			     teachers_load.id as teachers_load_id,                         
			     teachers_load.direction_id as direction_id,
			     teachers_load.teachers_id as teachers_id,
			     teachers_load.load_time as load_time
	         from studyplan_subject
             inner join studyplan on (studyplan_subject.studyplan_id = studyplan.id)
             inner join guide_subject_vid on (guide_subject_vid.id = studyplan_subject.subject_vid_id and guide_subject_vid.qty_min = 1 and guide_subject_vid.qty_max = 1)
             left join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id 
				  and teachers_load.subject_sect_studyplan_id = 0)
           )
UNION ALL
         (select studyplan_subject.id as studyplan_subject_id,
                 studyplan_subject.week_time as week_time,
                 subject_sect_studyplan.id as subject_sect_studyplan_id,
                 studyplan.id as studyplan_id,
                 studyplan.student_id as student_id,
                 studyplan.plan_year as plan_year,
                 studyplan.status as status,
                 teachers_load.id as teachers_load_id,                         
                 teachers_load.direction_id as direction_id,
                 teachers_load.teachers_id as teachers_id,
                 teachers_load.load_time as load_time
             from studyplan_subject
             inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
             left join subject_sect on (subject_sect.subject_cat_id = studyplan_subject.subject_cat_id
                  and subject_sect.subject_id = studyplan_subject.subject_id
                  and subject_sect.subject_vid_id = studyplan_subject.subject_vid_id)
             inner join subject_sect_studyplan on (subject_sect_studyplan.subject_sect_id = subject_sect.id 
                  and studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 				   
             left join teachers_load  on (teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id
                  and teachers_load.studyplan_subject_id = 0)
                   )
ORDER BY studyplan_subject_id, subject_sect_studyplan_id, direction_id, teachers_id
  		   
        ')->execute();

    }

    public function down()
    {

        $this->db->createCommand()->dropView('teachers_load_view')->execute();
    }
}
