<?php


class m211119_191645_add_study_plan_subject_view extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['users_teachers', 'teachers_view', 'user_id', 'teachers_id', 'user_id', 'status', null, 'Преподаватели (ссылка на id учетной записи)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['union_name', 'education_union', 'id', 'union_name', 'union_name', 'status', null, 'Объединения программ'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_name', 'subject', 'id', 'name', 'name', 'status', null, 'Дисциплины(полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_name_dev', 'subject', 'id', 'slug', 'slug', 'status', null, 'Дисциплины(сокр)'],
        ])->execute();

        $this->db->createCommand()->createView('studyplan_subject_view', '
         select studyplan_subject.id as studyplan_subject_id,
                studyplan_subject.studyplan_id as studyplan_id,
                studyplan.student_id as student_id,
                studyplan.course as course,
                studyplan.plan_year as plan_year,
                guide_subject_category.name as subject_category_name,
                guide_subject_category.slug as subject_category_slug,
                subject.name as subject_name,
                subject.slug as subject_slug,
                guide_subject_vid.name as subject_vid_name,
                guide_subject_vid.slug as subject_vid_slug,
                guide_subject_type.name as subject_type_name,
                guide_subject_type.slug as subject_type_slug,
                education_programm.name as education_programm_name,
                education_programm.short_name as education_programm_short_name,
                guide_education_cat.name as education_cat_name,
                guide_education_cat.short_name as education_cat_short_name,
                concat(subject.name, \'(\',guide_subject_vid.slug, \' \',guide_subject_type.slug,\') \',guide_education_cat.short_name) as memo_1,
                concat(subject.name, \'(\',guide_subject_category.slug, \' \',guide_subject_type.slug,\')\') as memo_2
            from studyplan_subject
            inner join studyplan on studyplan.id = studyplan_subject.studyplan_id
            inner join education_programm on education_programm.id = studyplan.programm_id
            inner join guide_education_cat on guide_education_cat.id = education_programm.education_cat_id
            inner join guide_subject_category on guide_subject_category.id = studyplan_subject.subject_cat_id
            inner join subject on subject.id = studyplan_subject.subject_id
            inner join guide_subject_vid on guide_subject_vid.id = studyplan_subject.subject_vid_id
            inner join guide_subject_type on guide_subject_type.id = studyplan_subject.subject_type_id
            order by subject.name
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_memo_1', 'studyplan_subject_view', 'studyplan_subject_id', 'memo_1', 'studyplan_id', null, null, 'Предмет ученика с хар-ми 1-й вид'],
        ])->execute();
        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_memo_2', 'studyplan_subject_view', 'studyplan_subject_id', 'memo_2', 'studyplan_id', null, null, 'Предмет ученика с хар-ми 2-й вид'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['studyplan_subject-student', 'studyplan_subject_view', 'studyplan_subject_id', 'student_id', 'studyplan_id', null, null, 'Предмет плана-ученик'],
        ])->execute();

        $this->db->createCommand()->createView('auditory_view', '
        SELECT auditory.id, building_id, cat_id, 
               auditory.num, auditory.name as auditory_name, guide_auditory_cat.name as cat_name, 
               guide_auditory_building.name as building_name, guide_auditory_building.slug as building_skug,
               auditory.floor, auditory.area, auditory.capacity, auditory.status, guide_auditory_cat.study_flag,
               concat(auditory.num, \' - \',auditory.name) as auditory_memo_1
            FROM auditory
            inner join guide_auditory_cat on guide_auditory_cat.id = auditory.cat_id
            inner join guide_auditory_building on guide_auditory_building.id = auditory.building_id
            where guide_auditory_cat.study_flag = 1
	        order by building_id, auditory.sort_order
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['auditory_memo_1', 'auditory_view', 'id', 'auditory_memo_1', 'auditory_name', 'status', null, 'Аудитории для обучения'],
        ])->execute();

     $this->db->createCommand()->createView('subject_sect_view', '
         select subject_sect_studyplan.id as id, 
		       concat(education_union.class_index, \' \', subject_sect_studyplan.class_name, \' (\',subject.name, \'-\',guide_subject_category.slug, \') \') as sect_name_1
         from subject_sect_studyplan
         inner join subject_sect on subject_sect.id = subject_sect_studyplan.subject_sect_id
         inner join guide_subject_category on guide_subject_category.id = subject_sect.subject_cat_id
         inner join subject on subject.id = subject_sect.subject_id
         inner join education_union on education_union.id = subject_sect.union_id
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['sect_name_1', 'subject_sect_view', 'id', 'sect_name_1', 'sect_name_1', null, null, 'Название группы'],
        ])->execute();

    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_name_1'])->execute();
        $this->db->createCommand()->dropView('subject_sect_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'auditory_memo_1'])->execute();
        $this->db->createCommand()->dropView('auditory_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'studyplan_subject-student'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_memo_2'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_memo_1'])->execute();
        $this->db->createCommand()->dropView('studyplan_subject_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'union_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'users_teachers'])->execute();

    }
}
