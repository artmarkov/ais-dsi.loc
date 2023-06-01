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
    education_programm.name AS education_programm_name,
    education_programm.short_name AS education_programm_short_name,
    guide_education_cat.name AS education_cat_name,
    guide_education_cat.short_name AS education_cat_short_name,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name, \' \') AS student_fio,
    guide_subject_form.name AS subject_form_name
   FROM studyplan
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     JOIN guide_subject_form ON guide_subject_form.id = studyplan.subject_form_id;
        ')->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand()->dropView('studyplan_view')->execute();
        $this->db->createCommand()->dropView('subject_view')->execute();
    }


}
