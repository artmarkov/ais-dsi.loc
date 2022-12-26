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
            ['subject_name', 'subject', 'id', 'name', 'name', 'status', null, 'Предметы(полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_name_dev', 'subject', 'id', 'slug', 'slug', 'status', null, 'Предметы(сокр)'],
        ])->execute();

        $this->db->createCommand()->createView('studyplan_subject_view', '
            SELECT DISTINCT * FROM (SELECT studyplan_subject.id AS studyplan_subject_id,
    NULL::integer AS subject_sect_studyplan_id,
	NULL::integer AS subject_sect_id,
    studyplan_subject.studyplan_id,
    studyplan_subject.week_time,
    studyplan_subject.year_time,
    studyplan_subject.cost_month_summ,
    studyplan.student_id,
    studyplan.course,
    studyplan.plan_year,
    guide_subject_category.name AS subject_category_name,
    guide_subject_category.slug AS subject_category_slug,
    subject.id AS subject_id,
    subject.name AS subject_name,
    subject.slug AS subject_slug,
    guide_subject_vid.name AS subject_vid_name,
    guide_subject_vid.slug AS subject_vid_slug,
    guide_subject_type.name AS subject_type_name,
    guide_subject_type.slug AS subject_type_slug,
    education_programm.name AS education_programm_name,
    education_programm.short_name AS education_programm_short_name,
    guide_education_cat.name AS education_cat_name,
    guide_education_cat.short_name AS education_cat_short_name,
    user_common.status,
    students.position_id,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_1,
    concat(subject.name, \'(\', guide_subject_category.slug, \' \', guide_subject_type.slug, \')\') AS memo_2,
    concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') AS memo_3,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. - \', subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_4
   FROM studyplan_subject
     JOIN studyplan ON studyplan_subject.studyplan_id = studyplan.id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
UNION ALL
 SELECT studyplan_subject.id AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
	subject_sect_studyplan.subject_sect_id AS subject_sect_id,
    studyplan_subject.studyplan_id,
    studyplan_subject.week_time,
    studyplan_subject.year_time,
    studyplan_subject.cost_month_summ,
    studyplan.student_id,
    studyplan.course,
    studyplan.plan_year,
    guide_subject_category.name AS subject_category_name,
    guide_subject_category.slug AS subject_category_slug,
    subject.id AS subject_id,
    subject.name AS subject_name,
    subject.slug AS subject_slug,
    guide_subject_vid.name AS subject_vid_name,
    guide_subject_vid.slug AS subject_vid_slug,
    guide_subject_type.name AS subject_type_name,
    guide_subject_type.slug AS subject_type_slug,
    education_programm.name AS education_programm_name,
    education_programm.short_name AS education_programm_short_name,
    guide_education_cat.name AS education_cat_name,
    guide_education_cat.short_name AS education_cat_short_name,
    user_common.status,
    students.position_id,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_1,
    concat(subject.name, \'(\', guide_subject_category.slug, \' \', guide_subject_type.slug, \')\') AS memo_2,
    concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') AS memo_3,
    concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. - \', subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_4
   FROM studyplan_subject
     JOIN studyplan ON studyplan_subject.studyplan_id = studyplan.id
     LEFT JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
     LEFT JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
  ) AS tmp_table ORDER BY 1;
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_memo_1', 'studyplan_subject_view', 'studyplan_subject_id', 'memo_1', 'studyplan_id', null, null, 'Предмет ученика с хар-ми 1-й вид'],
        ])->execute();
        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_memo_2', 'studyplan_subject_view', 'studyplan_subject_id', 'memo_2', 'studyplan_id', 'studyplan_id', null, 'Предмет ученика с хар-ми 2-й вид'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_memo_3', 'studyplan_subject_view', 'studyplan_subject_id', 'memo_3', 'studyplan_id', 'studyplan_id', null, 'Предмет ученика с хар-ми 3-й вид'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_memo_4', 'studyplan_subject_view', 'studyplan_subject_id', 'memo_4', 'studyplan_id', 'studyplan_id', null, 'Предмет ученика с хар-ми 4-й вид'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['studyplan_subject-student', 'studyplan_subject_view', 'studyplan_subject_id', 'student_id', 'studyplan_id', null, null, 'Предмет плана-ученик'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['studyplan_subject-student_fio', 'studyplan_subject_view', 'studyplan_subject_id', 'student_fio', 'student_fio', null, null, 'Предмет плана-ученик'],
        ])->execute();

        $this->db->createCommand()->createView('auditory_view', '
        SELECT auditory.id, auditory.sort_order, building_id, cat_id, 
               auditory.num, auditory.name as auditory_name, guide_auditory_cat.name as cat_name, 
               guide_auditory_building.name as building_name, guide_auditory_building.slug as building_slug,
               auditory.floor, auditory.area, auditory.capacity, auditory.status, guide_auditory_cat.study_flag,
               concat(auditory.num, \' - \',auditory.name) as auditory_memo_1
            FROM auditory
            inner join guide_auditory_cat on guide_auditory_cat.id = auditory.cat_id
            inner join guide_auditory_building on guide_auditory_building.id = auditory.building_id
            where guide_auditory_cat.study_flag = 1
	        order by auditory.sort_order
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['auditory_memo_1', 'auditory_view', 'id', 'auditory_memo_1', 'sort_order', 'study_flag', 'building_name', 'Аудитории для обучения'],
        ])->execute();

        $this->db->createCommand()->createView('subject_sect_view', '
          SELECT subject_sect_studyplan.id,
            subject_sect.id AS subject_sect_id,
            subject_sect_studyplan.plan_year,
            subject_sect_studyplan.course,
            subject_sect_studyplan.group_num,
            subject_sect_studyplan.subject_type_id,
            subject_sect_studyplan.studyplan_subject_list,
            concat(subject_sect_studyplan.course, \'/\', education_union.term_mastering, \'-\', to_char(subject_sect_studyplan.group_num, \'fm00\'::text)) AS sect_memo_1,
            concat(subject.name, \' (\', subject_sect_studyplan.course, \'/\', education_union.term_mastering, \'-\', to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \')\') AS sect_memo_2,
            concat(education_union.class_index, \' \', subject_sect.sect_name, \' (\', subject_sect_studyplan.course, \'/\', education_union.term_mastering, \'-\', to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name_1,
            concat(education_union.class_index, \' \', subject_sect.sect_name, \' (\', guide_subject_category.slug, \') \', subject_sect_studyplan.course, \'/\', education_union.term_mastering, \'-\', to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \' \', guide_subject_vid.slug, \' \', guide_subject_type.slug) AS sect_name_2,
            concat(subject.name, \' (\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \')\') AS sect_name_3
           FROM subject_sect_studyplan
             JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
             JOIN guide_subject_category ON guide_subject_category.id = subject_sect.subject_cat_id
             JOIN subject ON subject.id = subject_sect.subject_id
             JOIN guide_subject_vid ON guide_subject_vid.id = subject_sect.subject_vid_id
             JOIN guide_subject_type ON guide_subject_type.id = subject_sect_studyplan.subject_type_id
             JOIN education_union ON education_union.id = subject_sect.union_id;
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
            ['sect_name_4', 'subject_sect_view', 'subject_sect_id', 'sect_name_3', 'sect_name_3', null, null, 'Название группы'],
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
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_name_4'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_name_2'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'sect_name_1'])->execute();
        $this->db->createCommand()->dropView('subject_sect_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'auditory_memo_1'])->execute();
        $this->db->createCommand()->dropView('auditory_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'studyplan_subject-student_fio'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'studyplan_subject-student'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_memo_4'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_memo_3'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_memo_2'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_memo_1'])->execute();
        $this->db->createCommand()->dropView('studyplan_subject_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'union_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'users_teachers'])->execute();
    }
}
