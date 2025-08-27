<?php

use yii\db\Migration;

/**
 * Class m230127_092207_add_subject_view
 */
class m230127_092207_add_subject_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand()->createView('subject_view', '
 SELECT subject.id AS subject_id,
    guide_subject_category.id AS category_id,
    guide_subject_vid.id AS vid_id,
    guide_department.id AS department_id,
	guide_division.id AS division_id,
    subject.name AS subject_name,
    subject.slug AS subject_slug,
    guide_subject_category.name AS category_name,
    guide_subject_category.slug AS category_slug,
    guide_subject_category.dep_flag AS category_dep_flag,
    guide_subject_vid.name AS vid_name,
    guide_subject_vid.slug AS vid_slug,
    guide_subject_vid.qty_min AS vid_qty_min,
    guide_subject_vid.qty_max AS vid_qty_max,
    guide_department.name AS department_name,
    guide_department.slug AS department_slug,
	guide_division.name as division_name,
	guide_division.slug AS division_slug
   FROM guide_subject_category,
    guide_subject_vid,
    guide_department,
	guide_division,
    subject	
  WHERE (guide_subject_category.id = ANY (string_to_array(subject.category_list::text, \',\'::text)::integer[])) 
  AND (guide_subject_vid.id = ANY (string_to_array(subject.vid_list::text, \',\'::text)::integer[])) 
  AND (guide_department.id = ANY (string_to_array(subject.department_list::text, \',\'::text)::integer[]))
  AND guide_division.id = guide_department.division_id
  ORDER BY guide_subject_category.id, guide_subject_vid.id, subject.name;
        ')->execute();

        $this->db->createCommand()->createView('studyplan_view', '
    SELECT studyplan.id,
    studyplan.student_id,
    studyplan.programm_id,
    studyplan.subject_form_id,
    studyplan.course,
    studyplan.plan_year,
    studyplan.description,
    studyplan.year_time_total,
    studyplan.cost_month_total,
    studyplan.cost_year_total,
    studyplan.doc_date,
    studyplan.doc_contract_start,
    studyplan.doc_contract_end,
    studyplan.doc_signer,
    studyplan.doc_received_flag,
    studyplan.doc_sent_flag,
    studyplan.status,
    studyplan.status_reason,
    studyplan.cond_flag,
    education_programm.name AS education_programm_name,
    education_programm.short_name AS education_programm_short_name,
    guide_education_cat.id AS education_cat_id,
    guide_education_cat.name AS education_cat_name,
    guide_education_cat.short_name AS education_cat_short_name,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name, \' \') AS student_fio,
    guide_subject_form.name AS subject_form_name,
    ( SELECT subject.name
           FROM studyplan_subject
             JOIN subject ON studyplan_subject.subject_id = subject.id
          WHERE studyplan_subject.subject_cat_id = 1000 AND studyplan_subject.studyplan_id = studyplan.id
         LIMIT 1) AS speciality,
        CASE
            WHEN user_common.phone IS NOT NULL THEN user_common.phone
            WHEN user_common.phone_optional IS NOT NULL THEN user_common.phone_optional
            ELSE NULL::character varying
        END AS user_phone
   FROM studyplan
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     LEFT JOIN guide_subject_form ON guide_subject_form.id = studyplan.subject_form_id;
        ')->execute();

        $this->db->createCommand()->createView('studyplan_stat_view', '
 SELECT studyplan.id,
    studyplan.created_at AS studyplan_created_at,
    s.created_at AS student_created_at,
    studyplan.student_id,
    studyplan.programm_id,
    studyplan.subject_form_id,
    studyplan.course,
    studyplan.plan_year,
    studyplan.description,
    studyplan.year_time_total,
    studyplan.cost_month_total,
    studyplan.cost_year_total,
    studyplan.doc_date,
    studyplan.doc_contract_start,
    studyplan.doc_contract_end,
    studyplan.doc_signer,
    studyplan.doc_received_flag,
    studyplan.doc_sent_flag,
    studyplan.status,
    studyplan.status_reason,
    education_programm.name AS education_programm_name,
    education_programm.short_name AS education_programm_short_name,
    guide_education_cat.name AS education_cat_name,
    guide_education_cat.short_name AS education_cat_short_name,
    ( SELECT subject.name
           FROM studyplan_subject
             JOIN subject ON studyplan_subject.subject_id = subject.id
          WHERE studyplan_subject.subject_cat_id = 1000 AND studyplan_subject.studyplan_id = studyplan.id
         LIMIT 1) AS speciality,
    ( SELECT concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS concat
           FROM teachers_load_studyplan_view
             JOIN teachers ON teachers_load_studyplan_view.teachers_id = teachers.id
             JOIN user_common ON user_common.id = teachers.user_common_id
          WHERE teachers_load_studyplan_view.direction_id = 1000 AND teachers_load_studyplan_view.studyplan_subject_id = (( SELECT studyplan_subject.id
                   FROM studyplan_subject
                     JOIN subject ON studyplan_subject.subject_id = subject.id
                  WHERE studyplan_subject.subject_cat_id = 1000 AND studyplan_subject.studyplan_id = studyplan.id
                 LIMIT 1))
         LIMIT 1) AS speciality_teachers_fio,
    concat(s.last_name, \' \', s.first_name, \' \', s.middle_name, \' \') AS student_fio,
    guide_subject_form.name AS subject_form_name,
    s.address AS student_address,
    s.birth_date AS student_birth_date,
    s.gender AS student_gender,
    s.phone AS student_phone,
    s.phone_optional AS student_phone_optional,
    s.snils AS student_snils,
    s.info AS student_info,
    s.email AS student_email,
    students.sert_name AS student_sert_name,
    students.sert_series AS student_sert_series,
    students.sert_num AS student_sert_num,
    students.sert_organ AS student_sert_organ,
    students.sert_date AS student_sert_date,
    students.limited_status_list,
    concat(p.last_name, \' \', p.first_name, \' \', p.middle_name, \' \') AS signer_fio,
    p.address AS signer_address,
    p.birth_date AS signer_birth_date,
    p.gender AS signer_gender,
    p.phone AS signer_phone,
    p.phone_optional AS signer_phone_optional,
    p.snils AS signer_snils,
    p.info AS signer_info,
    p.email AS signer_email,
    parents.sert_name AS signer_sert_name,
    parents.sert_series AS signer_sert_series,
    parents.sert_num AS signer_sert_num,
    parents.sert_organ AS signer_sert_organ,
    parents.sert_date AS signer_sert_date,
    parents.sert_code AS signer_sert_code,
    s.last_name AS student_last_name,
    s.first_name AS student_first_name,
    s.middle_name AS student_middle_name
   FROM studyplan
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common s ON s.id = students.user_common_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     LEFT JOIN guide_subject_form ON guide_subject_form.id = studyplan.subject_form_id
     LEFT JOIN parents ON parents.id = (( SELECT student_dependence.parent_id
           FROM student_dependence
          WHERE student_dependence.student_id = studyplan.student_id
         LIMIT 1))
     LEFT JOIN user_common p ON p.id = parents.user_common_id
  ORDER BY studyplan.plan_year, (concat(s.last_name, \' \', s.first_name, \' \', s.middle_name, \' \'));
        ')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand()->dropView('studyplan_stat_view')->execute();
        $this->db->createCommand()->dropView('studyplan_view')->execute();
        $this->db->createCommand()->dropView('subject_view')->execute();
    }


}
