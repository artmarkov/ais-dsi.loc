<?php


class m210824_135345_ref extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_category_name', 'guide_subject_category', 'id', 'name', 'id', 'status', null, 'Раздел дисциплины (полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_category_name_dev', 'guide_subject_category', 'id', 'slug', 'id', 'status', null, 'Раздел дисциплины (кратко)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_vid_name', 'guide_subject_vid', 'id', 'name', 'id', 'status', null, 'Форма занятий (полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_vid_name_dev', 'guide_subject_vid', 'id', 'slug', 'id', 'status', null, 'Форма занятий (кратко)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_type_name', 'guide_subject_type', 'id', 'name', 'id', 'status', null, 'Тип занятий (полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_type_name_dev', 'guide_subject_type', 'id', 'slug', 'id', 'status', null, 'Тип занятий (кратко)'],
        ])->execute();

        $this->db->createCommand()->createView('subject_view', '
          SELECT
                subject.id as subject_id,
                subject.name as subject_name,
                subject.slug as subject_slug,
                subject.status as subject_status,
                guide_subject_category.id as subject_category_id,
                guide_subject_category.name as subject_category_name,
                guide_subject_category.slug as subject_category_slug,
                guide_department.id as department_id,
                guide_department.name as department_name,
                guide_department.slug as department_slug,
                guide_subject_vid.id as subject_vid_id,
                guide_subject_vid.name as subject_vid_name,
                guide_subject_vid.slug as subject_vid_slug,
                CONCAT(subject.name, \' (\', guide_department.slug, \')\') as subject_dep_name
            FROM subject, guide_subject_category, guide_department, guide_subject_vid
            where guide_subject_category.id::char = ANY(string_to_array(category_list,\',\'))
            AND guide_department.id::char = ANY(string_to_array(department_list,\',\'))
            AND guide_subject_vid.id::char = ANY(string_to_array(vid_list,\',\'))
        ')->execute();

        $this->db->createCommand()->createView('programm_department_view', 'SELECT
                education_programm.id as programm_id,
				education_programm.status as programm_status,
				education_speciality.id as speciality_id,
				education_speciality.status as speciality_status,
				guide_department.id as department_id,
				guide_department.status as department_status,
				guide_department.division_id as department_division_id,
				guide_department.name as department_name,
				guide_department.slug as department_slug
            FROM education_programm, education_speciality, guide_department
            where education_speciality.id::char = ANY(string_to_array(speciality_list,\',\'))
			AND guide_department.id::char = ANY(string_to_array(department_list,\',\'))
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['programm_department', 'programm_department_view', 'department_id', 'department_id', 'department_id', 'programm_id', null, 'Все отделы выбранной программы'],
        ])->execute();
    }
    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'programm_department'])->execute();
        $this->db->createCommand()->dropView('programm_department_view')->execute();
        $this->db->createCommand()->dropView('subject_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_category_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_category_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_vid_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_vid_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_type_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_type_name_dev'])->execute();
    }
}