<?php


class m220214_204015_ref extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $this->db->createCommand()->createView('teachers_load_studyplan_view', '
         (select studyplan_subject.id as studyplan_subject_id,
			     studyplan_subject.week_time as week_time,
			     0 as subject_sect_studyplan_id,
			     studyplan_subject.id::text as studyplan_subject_list,
			     0 as subject_sect_id,
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
                 subject_sect_studyplan.studyplan_subject_list as studyplan_subject_list,
                 subject_sect.id as subject_sect_id,
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

        $this->db->createCommand()->createView('teachers_load_view', '
         (select studyplan_subject.id as studyplan_subject_id,
                 0 as subject_sect_studyplan_id,
                 studyplan_subject.id::text as studyplan_subject_list,
                 0 as subject_sect_id,
                 studyplan.plan_year as plan_year,
                 studyplan_subject.week_time as week_time,
                 teachers_load.id as teachers_load_id,                         
                 teachers_load.direction_id as direction_id,
                 teachers_load.teachers_id as teachers_id,
                 teachers_load.load_time as load_time
             from studyplan_subject
			 inner join studyplan on (studyplan.id = studyplan_subject.studyplan_id)
			 left join teachers_load on (teachers_load.studyplan_subject_id = studyplan_subject.id 
			 and teachers_load.subject_sect_studyplan_id = 0)
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
                 teachers_load.load_time as load_time
             from subject_sect_studyplan
			 inner join subject_sect on (subject_sect.id = subject_sect_studyplan.subject_sect_id)
			 left join teachers_load on (subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id 
			  and teachers_load.studyplan_subject_id = 0)
			  )
ORDER BY subject_sect_studyplan_id, direction_id, teachers_id
        ')->execute();

        $this->db->createCommand()->createView('subject_sect_view', '
         select subject_sect_studyplan.id as id, 
    	       concat(subject.name, \'(\',guide_subject_vid.slug,\') \') as sect_memo_1,
               concat(subject.name, \'(\',guide_subject_category.slug, \' \',guide_subject_vid.slug,\')\') as sect_memo_2,
		       concat(education_union.class_index, \' \', subject_sect_studyplan.class_name, \' (\',subject.name, \'-\',guide_subject_category.slug, \') \') as sect_name_1,
		       concat(education_union.class_index, \' \', subject_sect_studyplan.class_name, \' (\',subject.name, \'-\',guide_subject_category.slug, \') \',\' \',guide_subject_vid.slug,\' \', guide_subject_type.slug) as sect_name_2,
			   concat(education_union.class_index, \' \', subject_sect_studyplan.class_name, \' (\',guide_subject_type.slug, \') \') as sect_name_3
         from subject_sect_studyplan
         inner join subject_sect on subject_sect.id = subject_sect_studyplan.subject_sect_id
         inner join guide_subject_category on guide_subject_category.id = subject_sect.subject_cat_id
         inner join subject on subject.id = subject_sect.subject_id
		 inner join guide_subject_vid on guide_subject_vid.id = subject_sect.subject_vid_id
		 inner join guide_subject_type on guide_subject_type.id = subject_sect_studyplan.subject_type_id
         inner join education_union on education_union.id = subject_sect.union_id
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['sect_name_1', 'subject_sect_view', 'id', 'sect_name_1', 'sect_name_1', null, null, 'Название группы'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['sect_name_2', 'subject_sect_view', 'id', 'sect_name_2', 'sect_name_2', null, null, 'Название группы с типом занятий'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['sect_name_3', 'subject_sect_view', 'id', 'sect_name_3', 'sect_name_3', null, null, 'Название группы'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['sect_memo_1', 'subject_sect_view', 'id', 'sect_memo_1', 'sect_memo_1', null, null, 'Название группы'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['sect_memo_2', 'subject_sect_view', 'id', 'sect_memo_2', 'sect_memo_2', null, null, 'Название группы'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_memo_2'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_memo_1'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_name_3'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_name_2'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_name_1'])->execute();
        $this->db->createCommand()->dropView('subject_sect_view')->execute();
        $this->db->createCommand()->dropView('teachers_load_view')->execute();
        $this->db->createCommand()->dropView('teachers_load_studyplan_view')->execute();
    }
}
