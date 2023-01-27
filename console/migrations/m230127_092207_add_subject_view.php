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

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand()->dropView('subject_view')->execute();
    }


}
