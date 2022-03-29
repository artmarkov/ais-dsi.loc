<?php


class m220214_204015_ref extends \artsoft\db\BaseMigration
{

    public function up()
    {


        $this->db->createCommand()->createView('lesson_progress_view', '
(select studyplan_subject.id as studyplan_subject_id,
				0 as subject_sect_studyplan_id,
 				0 as subject_sect_id,
 				studyplan.plan_year as plan_year,				
       			studyplan.id as studyplan_id,
       			studyplan.student_id as student_id,
				array_to_string(ARRAY(select teachers_id from teachers_load where studyplan_subject_id = studyplan_subject.id and subject_sect_studyplan_id = 0), \',\')::text as teachers_list
             from studyplan_subject 
			 inner join guide_subject_vid on (guide_subject_vid.id = studyplan_subject.subject_vid_id and guide_subject_vid.qty_min = 1 and guide_subject_vid.qty_max = 1)
             inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
				 )
UNION ALL 
(select studyplan_subject.id as studyplan_subject_id,
				subject_sect_studyplan.id as subject_sect_studyplan_id,
 				subject_sect.id as subject_sect_id,
 				subject_sect.plan_year as plan_year,
			    studyplan.id as studyplan_id,
			    studyplan.student_id as student_id,
				array_to_string(ARRAY(select teachers_load.teachers_id from teachers_load where subject_sect_studyplan_id = subject_sect_studyplan.id and studyplan_subject_id = 0), \',\')::text as teachers_list
             from subject_sect_studyplan
             inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
             inner join studyplan_subject on (studyplan_subject.id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\')::int[])) 				   
             inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)			
				 )
ORDER BY studyplan_id, subject_sect_studyplan_id, studyplan_subject_id
        ')->execute();

        $this->db->createCommand()->createView('lesson_items_progress_view', '
(select 0 as subject_sect_studyplan_id,
                lesson_progress.studyplan_subject_id,
 				studyplan_subject.studyplan_id,
                0 as subject_sect_id,
                lesson_items.id as lesson_items_id,
                lesson_items.lesson_date,
                lesson_items.lesson_topic,
                lesson_items.lesson_rem,	   
                lesson_progress.id as lesson_progress_id,
                lesson_progress.lesson_mark_id,
                guide_lesson_test.test_category,
                guide_lesson_test.test_name,
                guide_lesson_test.test_name_short,
                guide_lesson_test.plan_flag,
                guide_lesson_mark.mark_category,
                guide_lesson_mark.mark_label,
                guide_lesson_mark.mark_hint,
                guide_lesson_mark.mark_value,
                lesson_progress.mark_rem,
				array_to_string(ARRAY(select teachers_id from teachers_load where studyplan_subject_id = lesson_items.studyplan_subject_id and subject_sect_studyplan_id = 0), \',\')::text as teachers_list
             from lesson_items            
            inner join lesson_progress  on (lesson_progress.lesson_items_id = lesson_items.id and lesson_items.subject_sect_studyplan_id = 0) 
 			inner join studyplan_subject on (studyplan_subject.id = lesson_progress.studyplan_subject_id)
            left join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id)
            left join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id) 			
				 )
UNION ALL 
(select lesson_items.subject_sect_studyplan_id,
                lesson_progress.studyplan_subject_id,
 				studyplan_subject.studyplan_id,
                subject_sect.id as subject_sect_id,
                lesson_items.id as lesson_items_id,
                lesson_items.lesson_date,
                lesson_items.lesson_topic,
                lesson_items.lesson_rem,	   
                lesson_progress.id as lesson_progress_id,
                lesson_progress.lesson_mark_id,
                guide_lesson_test.test_category,
                guide_lesson_test.test_name,
                guide_lesson_test.test_name_short,
                guide_lesson_test.plan_flag,
                guide_lesson_mark.mark_category,
                guide_lesson_mark.mark_label,
                guide_lesson_mark.mark_hint,
                guide_lesson_mark.mark_value,
                lesson_progress.mark_rem,
				array_to_string(ARRAY(select teachers_id from teachers_load where subject_sect_studyplan_id = lesson_items.subject_sect_studyplan_id and studyplan_subject_id = 0), \',\')::text as teachers_list
             from lesson_items
			 left join lesson_progress  on (lesson_progress.lesson_items_id = lesson_items.id and lesson_items.studyplan_subject_id = 0)
 			 inner join studyplan_subject on (studyplan_subject.id = lesson_progress.studyplan_subject_id)
             inner join subject_sect_studyplan  on (subject_sect_studyplan.id = lesson_items.subject_sect_studyplan_id)
             inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
             left join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id)
             left join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id) 
				 )
ORDER BY subject_sect_studyplan_id, studyplan_subject_id, lesson_date
        ')->execute();
    }



    public function down()
    {

        $this->db->createCommand()->dropView('lesson_items_progress_view')->execute();
        $this->db->createCommand()->dropView('lesson_progress_view')->execute();
    }
}
