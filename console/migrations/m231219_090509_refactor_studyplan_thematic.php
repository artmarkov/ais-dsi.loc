<?php

use yii\db\Migration;

/**
 * Class m231219_090509_refactor_studyplan_thematic
 */
class m231219_090509_refactor_studyplan_thematic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('studyplan_thematic_items', 'topic', $this->string(1024));
        $models = \common\models\studyplan\StudyplanThematicItems::find()->all();
        foreach ($models as $model) {
            $model->task = $this->replace_form($model->task);
            $model->topic = $model->author ? $this->replace_form($model->author) : '';
            $model->topic .= $model->piece_name ? ', ' . $this->replace_form($model->piece_name) : '';
            if ($model->topic == '') {
                $model->topic = $model->task;
                $model->task = '';
            }
            $model->save(false);
        }
        $this->db->createCommand()->dropView('thematic_view')->execute();
        $this->db->createCommand()->dropView('studyplan_thematic_view')->execute();
        $this->db->createCommand()->createView('thematic_view', '
         SELECT studyplan_subject.id AS studyplan_subject_id,
    0 AS subject_sect_studyplan_id,
    studyplan_subject.id::text AS studyplan_subject_list,
    studyplan_subject.subject_type_id,
    0 AS subject_sect_id,
    studyplan.plan_year,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    studyplan_thematic.id AS studyplan_thematic_id,
    studyplan_thematic.half_year,
    studyplan_thematic.doc_status,
    studyplan_thematic.doc_sign_teachers_id,
    studyplan_thematic.doc_sign_timestamp,
    studyplan_thematic.created_by AS author_id,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject,
    studyplan.status
   FROM studyplan
     JOIN studyplan_subject ON studyplan.id = studyplan_subject.studyplan_id
     LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     LEFT JOIN studyplan_thematic ON studyplan_thematic.studyplan_subject_id = studyplan_subject.id AND studyplan_thematic.subject_sect_studyplan_id = 0
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
UNION ALL
 SELECT 0 AS studyplan_subject_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    subject_sect_studyplan.studyplan_subject_list,
    subject_sect_studyplan.subject_type_id,
    subject_sect.id AS subject_sect_id,
    subject_sect_studyplan.plan_year,
    teachers_load.id AS teachers_load_id,
    teachers_load.direction_id,
    teachers_load.teachers_id,
    studyplan_thematic.id AS studyplan_thematic_id,
    studyplan_thematic.half_year,
    studyplan_thematic.doc_status,
    studyplan_thematic.doc_sign_teachers_id,
    studyplan_thematic.doc_sign_timestamp,
    studyplan_thematic.created_by AS author_id,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \') AS subject,
    subject_sect.status
   FROM subject_sect_studyplan
     JOIN subject_sect ON subject_sect.id = subject_sect_studyplan.subject_sect_id
     LEFT JOIN teachers_load ON subject_sect_studyplan.id = teachers_load.subject_sect_studyplan_id AND teachers_load.studyplan_subject_id = 0
     JOIN subject ON subject.id = subject_sect.subject_id
     LEFT JOIN guide_subject_type ON guide_subject_type.id = subject_sect.subject_type_id
     LEFT JOIN guide_subject_vid ON guide_subject_vid.id = subject_sect.subject_vid_id
     LEFT JOIN studyplan_thematic ON studyplan_thematic.subject_sect_studyplan_id = subject_sect_studyplan.id AND studyplan_thematic.studyplan_subject_id = 0
     ORDER BY 17, 16;
        ')->execute();
        $this->db->createCommand()->createView('studyplan_thematic_view', '
  SELECT studyplan.id AS studyplan_id,
    studyplan.student_id,
    studyplan.plan_year,
    studyplan.programm_id,
    studyplan.course,
    studyplan.status,
    studyplan_subject.id AS studyplan_subject_id,
    studyplan_subject.subject_cat_id,
    studyplan_subject.subject_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_vid_id,
    teachers_load.teachers_id,
    studyplan_thematic.id AS studyplan_thematic_id,
    studyplan_thematic.subject_sect_studyplan_id,
    studyplan_thematic.half_year,
    studyplan_thematic.doc_status,
    studyplan_thematic.doc_sign_teachers_id,
    studyplan_thematic.doc_sign_timestamp,
    studyplan_thematic.created_by AS author_id,
    concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \', guide_education_cat.short_name) AS subject
   FROM studyplan
     JOIN studyplan_subject ON studyplan.id = studyplan_subject.studyplan_id
     LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0 AND teachers_load.direction_id = 1000
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN education_programm ON education_programm.id = studyplan.programm_id
     JOIN guide_education_cat ON guide_education_cat.id = education_programm.education_cat_id
     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     LEFT JOIN studyplan_thematic ON studyplan_thematic.studyplan_subject_id = studyplan_subject.id AND studyplan_thematic.subject_sect_studyplan_id = 0
     JOIN students ON students.id = studyplan.student_id
     JOIN user_common ON user_common.id = students.user_common_id
UNION ALL
 SELECT studyplan.id AS studyplan_id,
    studyplan.student_id,
    studyplan.plan_year,
    studyplan.programm_id,
    studyplan.course,
    studyplan.status,
    studyplan_subject.id AS studyplan_subject_id,
    studyplan_subject.subject_cat_id,
    studyplan_subject.subject_id,
    studyplan_subject.subject_type_id,
    studyplan_subject.subject_vid_id,
    teachers_load.teachers_id,
    studyplan_thematic.id AS studyplan_thematic_id,
    subject_sect_studyplan.id AS subject_sect_studyplan_id,
    studyplan_thematic.half_year,
    studyplan_thematic.doc_status,
    studyplan_thematic.doc_sign_teachers_id,
    studyplan_thematic.doc_sign_timestamp,
    studyplan_thematic.created_by AS author_id,
    concat(subject_sect.sect_name, \' (\',
        CASE
            WHEN subject_sect_studyplan.course::text <> \'\'::text THEN concat(subject_sect_studyplan.course, \'/\', subject_sect.term_mastering, \'_\')
            ELSE \'\'::text
        END, to_char(subject_sect_studyplan.group_num, \'fm00\'::text), \') \') AS sect_name,
    concat(subject.name, \'(\', guide_subject_vid.slug, \' \', guide_subject_type.slug, \') \') AS subject
   FROM studyplan
     JOIN studyplan_subject ON studyplan_subject.studyplan_id = studyplan.id
     LEFT JOIN teachers_load ON teachers_load.studyplan_subject_id = studyplan_subject.id AND teachers_load.subject_sect_studyplan_id = 0
     LEFT JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
     JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
     JOIN subject ON subject.id = studyplan_subject.subject_id
     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
     LEFT JOIN studyplan_thematic ON studyplan_thematic.subject_sect_studyplan_id = subject_sect_studyplan.id AND studyplan_thematic.studyplan_subject_id = 0
     ORDER BY 21, 20;

        ')->execute();
        $this->dropForeignKey('studyplan_thematic_items_ibfk_2', 'studyplan_thematic_items');

        $this->dropColumn('studyplan_thematic_items', 'author');
        $this->dropColumn('studyplan_thematic_items', 'piece_name');
        $this->dropColumn('studyplan_thematic_items', 'piece_category_id');
        $this->dropColumn('studyplan_thematic', 'thematic_category');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

    protected function replace_form($text)
    {
        $text = str_replace('&gt;', ">", $text);
        $text = str_replace('&lt;', "<", $text);
        $text = str_replace('&quot;', '"', $text);
        $text = str_replace('&amp;', '', $text);
        return $text;
    }
}
