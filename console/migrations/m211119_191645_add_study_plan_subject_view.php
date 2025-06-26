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
            ['users_parents', 'parents_view', 'user_id', 'parents_id', 'user_id', 'status', null, 'Родители (ссылка на id учетной записи)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_name', 'subject', 'id', 'name', 'name', 'status', null, 'Предметы(полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_name_dev', 'subject', 'id', 'slug', 'slug', 'status', null, 'Предметы(сокр)'],
        ])->execute();

        $this->db->createCommand()->createView('studyplan_subject_view', '
        SELECT DISTINCT tmp_table.studyplan_subject_id,
    tmp_table.subject_sect_studyplan_id,
    tmp_table.subject_sect_id,
    tmp_table.studyplan_id,
    tmp_table.week_time,
    tmp_table.year_time,
    tmp_table.cost_month_summ,
    tmp_table.med_cert,
    tmp_table.fin_cert,
    tmp_table.student_id,
    tmp_table.course,
    tmp_table.plan_year,
    tmp_table.subject_category_id,
    tmp_table.subject_category_name,
    tmp_table.subject_category_slug,
    tmp_table.subject_id,
    tmp_table.subject_name,
    tmp_table.subject_slug,
    tmp_table.subject_vid_id,
    tmp_table.subject_vid_name,
    tmp_table.subject_vid_slug,
    tmp_table.subject_type_id,
    tmp_table.subject_type_name,
    tmp_table.subject_type_slug,
    tmp_table.education_programm_id,
    tmp_table.education_programm_name,
    tmp_table.education_programm_short_name,
    tmp_table.education_cat_id,
    tmp_table.education_cat_name,
    tmp_table.education_cat_short_name,
	tmp_table.education_cat_programm_short_name,
    tmp_table.status,
    tmp_table.status_reason,
    tmp_table.student_fio,
    tmp_table.student_fullname,
    tmp_table.student_fi,
    tmp_table.memo_1,
    tmp_table.memo_2,
    tmp_table.memo_3,
    tmp_table.memo_4,
    tmp_table.sect_name,
    tmp_table.speciality,
    concat(tmp_table.student_fi, \', \',
        CASE
            WHEN tmp_table.speciality::text <> \'\'::text THEN tmp_table.speciality::text
            ELSE tmp_table.subject_name::text
        END, \' \', tmp_table.course, tmp_table.education_cat_programm_short_name) AS memo_5
   FROM ( SELECT studyplan_subject.id AS studyplan_subject_id,
            NULL::integer AS subject_sect_studyplan_id,
            NULL::integer AS subject_sect_id,
            studyplan_subject.studyplan_id,
            studyplan_subject.week_time,
            studyplan_subject.year_time,
            studyplan_subject.cost_month_summ,
            studyplan_subject.med_cert,
            studyplan_subject.fin_cert,
            studyplan.student_id,
            studyplan.course,
            studyplan.plan_year,
            guide_subject_category.id AS subject_category_id,
            guide_subject_category.name AS subject_category_name,
            guide_subject_category.slug AS subject_category_slug,
            subject.id AS subject_id,
            subject.name AS subject_name,
            subject.slug AS subject_slug,
            guide_subject_vid.id AS subject_vid_id,
            guide_subject_vid.name AS subject_vid_name,
            guide_subject_vid.slug AS subject_vid_slug,
            guide_subject_type.id AS subject_type_id,
            guide_subject_type.name AS subject_type_name,
            guide_subject_type.slug AS subject_type_slug,
            education_programm.id AS education_programm_id,
            education_programm.name AS education_programm_name,
            education_programm.short_name AS education_programm_short_name,
            guide_education_cat.id AS education_cat_id,
            guide_education_cat.name AS education_cat_name,
            guide_education_cat.short_name AS education_cat_short_name,
            guide_education_cat.programm_short_name AS education_cat_programm_short_name,
            studyplan.status,
            studyplan.status_reason,
            concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
            concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name, \' \') AS student_fullname,
            concat(user_common.last_name, \' \', user_common.first_name) AS student_fi,
            concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_1,
            concat(subject.name, \'(\', guide_subject_category.slug, \' \', guide_subject_type.slug, \')\') AS memo_2,
            concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') AS memo_3,
            concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. - \', subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', studyplan.course, guide_education_cat.short_name) AS memo_4,
            \'Индивидуально\'::text AS sect_name,
            ( SELECT subject_1.slug
                   FROM studyplan_subject studyplan_subject_1
                     JOIN subject subject_1 ON studyplan_subject_1.subject_id = subject_1.id
                  WHERE studyplan_subject_1.subject_cat_id = 1000 AND studyplan_subject_1.studyplan_id = studyplan.id
                 LIMIT 1) AS speciality
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
            subject_sect_studyplan.subject_sect_id,
            studyplan_subject.studyplan_id,
            studyplan_subject.week_time,
            studyplan_subject.year_time,
            studyplan_subject.cost_month_summ,
            studyplan_subject.med_cert,
            studyplan_subject.fin_cert,
            studyplan.student_id,
            studyplan.course,
            studyplan.plan_year,
            guide_subject_category.id AS subject_category_id,
            guide_subject_category.name AS subject_category_name,
            guide_subject_category.slug AS subject_category_slug,
            subject.id AS subject_id,
            subject.name AS subject_name,
            subject.slug AS subject_slug,
            guide_subject_vid.id AS subject_vid_id,
            guide_subject_vid.name AS subject_vid_name,
            guide_subject_vid.slug AS subject_vid_slug,
            guide_subject_type.id AS subject_type_id,
            guide_subject_type.name AS subject_type_name,
            guide_subject_type.slug AS subject_type_slug,
            education_programm.id AS education_programm_id,
            education_programm.name AS education_programm_name,
            education_programm.short_name AS education_programm_short_name,
            guide_education_cat.id AS education_cat_id,
            guide_education_cat.name AS education_cat_name,
            guide_education_cat.short_name AS education_cat_short_name,
	        guide_education_cat.programm_short_name AS education_cat_programm_short_name,
            studyplan.status,
            studyplan.status_reason,
            concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio,
            concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name, \' \') AS student_fullname,
            concat(user_common.last_name, \' \', user_common.first_name) AS student_fi,
            concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS memo_1,
            concat(subject.name, \'(\', guide_subject_category.slug, \' \', guide_subject_type.slug, \')\') AS memo_2,
            concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') AS memo_3,
            concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'. - \', subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', studyplan.course, guide_education_cat.short_name) AS memo_4,
            concat(subject_sect.sect_name, \' (\',
                CASE
                    WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
                    ELSE \'\'::text
                END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \' \', guide_subject_type.slug, \') \') AS sect_name,
            ( SELECT subject_1.slug
                   FROM studyplan_subject studyplan_subject_1
                     JOIN subject subject_1 ON studyplan_subject_1.subject_id = subject_1.id
                  WHERE studyplan_subject_1.subject_cat_id = 1000 AND studyplan_subject_1.studyplan_id = studyplan.id
                 LIMIT 1) AS speciality
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
             JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id) tmp_table
  ORDER BY tmp_table.studyplan_subject_id;
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
               auditory.floor, auditory.area, auditory.capacity, auditory.status, auditory.study_flag,
               concat(auditory.num, \' - \',auditory.name) as auditory_memo_1
            FROM auditory
            inner join guide_auditory_cat on guide_auditory_cat.id = auditory.cat_id
            inner join guide_auditory_building on guide_auditory_building.id = auditory.building_id
            where auditory.study_flag = true 
	        order by auditory.sort_order
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['auditory_memo_1', 'auditory_view', 'id', 'auditory_memo_1', 'sort_order', 'study_flag', 'building_name', 'Аудитории для обучения'],
        ])->execute();

        $this->db->createCommand()->createView('subject_sect_view', '
 SELECT sect.id,
    sect.subject_sect_id,
    sect.term_mastering,
    sect.plan_year,
    sect.course,
    sect.group_num,
    sect.subject_id,
    sect.subject_type_id,
    sect.studyplan_subject_list,
    concat(sect.course_text, sect.class_text, to_char(sect.group_num, \'fm00\'::text)) AS sect_memo_1,
    concat(sect.subject_name, \' (\', sect.course_text, sect.class_text, to_char(sect.group_num, \'fm00\'::text), \')\') AS sect_memo_2,
    concat(sect.group_name, \' (\', sect.course_text, to_char(sect.group_num, \'fm00\'::text), \') \') AS sect_name_1,
    concat(sect.group_name, \' (\', sect.course_text, sect.class_text, to_char(sect.group_num, \'fm00\'::text), \') \', \' \', sect.vid_slug, \' \', sect.type_slug) AS sect_name_2,
    concat(sect.subject_name, \' (\', sect.vid_slug, \' \', sect.type_slug, \')\') AS sect_name_3,
    concat(sect.subject_name, \' (\', sect.vid_slug, \')\') AS sect_name_4
   FROM ( SELECT subject_sect_studyplan.id,
            subject_sect.id AS subject_sect_id,
            subject_sect.term_mastering,
            subject_sect_studyplan.plan_year,
            subject_sect_studyplan.course,
            subject_sect_studyplan.group_num,
		    subject_sect.subject_id as subject_id,
            subject_sect_studyplan.subject_type_id,
            subject_sect_studyplan.studyplan_subject_list,
            guide_subject_vid.slug AS vid_slug,
            guide_subject_type.slug AS type_slug,
            subject_sect.sect_name AS group_name,
            subject.name AS subject_name,
                CASE
                    WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
                    ELSE \'\'::text
                END AS course_text,
                CASE
                    WHEN subject_sect.class_index::text <> \'\'::text THEN concat(subject_sect.class_index, \'-\')
                    ELSE \'\'::text
                END AS class_text
           FROM subject_sect_studyplan
             JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
             JOIN guide_subject_category ON guide_subject_category.id = subject_sect.subject_cat_id
             JOIN subject ON subject.id = subject_sect.subject_id
             JOIN guide_subject_vid ON guide_subject_vid.id = subject_sect.subject_vid_id
             JOIN guide_subject_type ON guide_subject_type.id = subject_sect_studyplan.subject_type_id) sect;
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
            ['sect_name_4', 'subject_sect_view', 'subject_sect_id', 'sect_name_4', 'sect_name_4', null, null, 'Название группы'],
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
        $this->db->createCommand()->delete('refbooks', ['name' => 'users_parents'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'users_teachers'])->execute();
    }
}
