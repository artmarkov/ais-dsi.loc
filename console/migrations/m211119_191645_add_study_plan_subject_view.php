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
                concat(subject.name, \'(\',guide_subject_vid.slug, \' \',guide_subject_type.slug,\') \',guide_education_cat.short_name) as memo_1
            from studyplan_subject
            inner join studyplan on studyplan.id = studyplan_subject.studyplan_id
            inner join education_programm on education_programm.id = studyplan.programm_id
            inner join guide_education_cat on guide_education_cat.id = education_programm.education_cat_id
            inner join guide_subject_category on guide_subject_category.id = studyplan_subject.subject_cat_id
            inner join subject on subject.id = studyplan_subject.subject_id
            inner join guide_subject_vid on guide_subject_vid.id = studyplan_subject.subject_vid_id
            inner join guide_subject_type on guide_subject_type.id = studyplan_subject.subject_type_id
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_memo_1', 'studyplan_subject_view', 'studyplan_subject_id', 'memo_1', 'studyplan_id', null, null, 'Ученики (Фамилия И.О.)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['studyplan_subject-student', 'studyplan_subject_view', 'studyplan_subject_id', 'student_id', 'studyplan_id', null, null, 'Дисциплина плана-ученик'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'studyplan_subject'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_memo_1'])->execute();
        $this->db->createCommand()->dropView('studyplan_subject_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'union_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'users_teachers'])->execute();

    }
}
