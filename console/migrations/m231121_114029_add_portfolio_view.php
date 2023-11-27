<?php

use yii\db\Migration;

/**
 * Class m231121_114029_add_portfolio_view
 */
class m231121_114029_add_portfolio_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand()->createView('portfolio_view', '
 SELECT schoolplan_perform.id AS schoolplan_perform_id,
    schoolplan.id AS schoolplan_id,
    schoolplan.title,
    schoolplan.datetime_in,
    schoolplan.datetime_out,
    schoolplan.category_id,
    schoolplan.doc_status,
    schoolplan_perform.studyplan_id,
    schoolplan_perform.studyplan_subject_id,
    schoolplan_perform.teachers_id,
    guide_lesson_mark.mark_label,
    schoolplan_perform.winner_id,
    schoolplan_perform.resume,
    schoolplan_perform.status_exe,
    schoolplan_perform.status_sign,
    schoolplan_perform.signer_id,
    schoolplan_perform.thematic_items_list,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name, \' (\', studyplan.course, \'/\', education_programm.term_mastering, \' \', education_programm.short_name, \')\') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject,
    studyplan_subject.subject_vid_id,
    studyplan.plan_year,
    studyplan.status AS studyplan_status
   FROM schoolplan
     JOIN schoolplan_perform ON schoolplan.id = schoolplan_perform.schoolplan_id
     LEFT JOIN studyplan ON studyplan.id = schoolplan_perform.studyplan_id
     LEFT JOIN studyplan_subject ON studyplan_subject.id = schoolplan_perform.studyplan_subject_id
     LEFT JOIN subject ON subject.id = studyplan_subject.subject_id
     LEFT JOIN education_programm ON education_programm.id = studyplan.programm_id
     LEFT JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     LEFT JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
     LEFT JOIN students ON students.id = studyplan.student_id
     LEFT JOIN user_common ON user_common.id = students.user_common_id
     LEFT JOIN guide_lesson_mark ON guide_lesson_mark.id = schoolplan_perform.lesson_mark_id
  ORDER BY schoolplan_perform.teachers_id, schoolplan.datetime_in;')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand()->dropView('portfolio_view')->execute();

        return false;
    }
}
