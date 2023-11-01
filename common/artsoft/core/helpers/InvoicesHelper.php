<?php

namespace artsoft\helpers;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class InvoicesHelper
 * @package artsoft\helpers
 *
 */
class InvoicesHelper
{
    protected $models;
    protected $model_date;
    protected $studyplanIds;
    protected $studyplanData;

    public static function getData($models, $model_date)
    {
        return new self($models, $model_date);
    }

    public function __construct($models, $model_date)
    {
        $this->models = $models;
        $this->model_date = $model_date;
        $this->studyplanIds = array_unique(\yii\helpers\ArrayHelper::getColumn($this->models, 'studyplan_id'));
        $this->studyplanData = $this->getStudyplanData(); // Запрос дисциплин выбранных планов ученика
//        print_r($this->studyplanData); die();
    }

    /**
     * Запрос дисциплин выбранных планов ученика
     * @return array
     * @throws \yii\db\Exception
     */
    public function getStudyplanData()
    {
        $query = \Yii::$app->db->createCommand('SELECT * FROM (SELECT studyplan_subject.subject_cat_id, teachers_load.direction_id, studyplan_subject.studyplan_id, studyplan_subject.subject_id, teachers_load.teachers_id, concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') as subject, concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS teacher
                   FROM studyplan_subject
                     JOIN teachers_load ON studyplan_subject.id = teachers_load.studyplan_subject_id AND teachers_load.subject_sect_studyplan_id = 0
                     JOIN teachers ON teachers.id = teachers_load.teachers_id
                     JOIN user_common ON user_common.id = teachers.user_common_id
                     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id AND guide_subject_vid.qty_min = 1 AND guide_subject_vid.qty_max = 1
                     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
                     JOIN subject ON subject.id = studyplan_subject.subject_id
                     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id
                UNION ALL
                 SELECT studyplan_subject.subject_cat_id, teachers_load.direction_id, studyplan_subject.studyplan_id, studyplan_subject.subject_id, teachers_load.teachers_id, concat(subject.name, \'(\', guide_subject_category.slug, \'&nbsp;\', guide_subject_type.slug, \')&nbsp;-&nbsp;\', guide_subject_vid.slug, \'&nbsp;\', studyplan_subject.week_time * 4::double precision, \'&nbsp;час/мес\') as subject, concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS teacher
                   FROM studyplan_subject
                     JOIN subject_sect ON subject_sect.subject_cat_id = studyplan_subject.subject_cat_id AND subject_sect.subject_id = studyplan_subject.subject_id AND subject_sect.subject_vid_id = studyplan_subject.subject_vid_id
                     JOIN subject_sect_studyplan ON subject_sect_studyplan.subject_sect_id = subject_sect.id AND (studyplan_subject.id = ANY (string_to_array(subject_sect_studyplan.studyplan_subject_list, \',\'::text)::integer[]))
                     JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = subject_sect_studyplan.id AND teachers_load.studyplan_subject_id = 0
                     JOIN teachers ON teachers.id = teachers_load.teachers_id
                     JOIN user_common ON user_common.id = teachers.user_common_id
                     JOIN guide_subject_vid ON guide_subject_vid.id = studyplan_subject.subject_vid_id
                     JOIN guide_subject_category ON guide_subject_category.id = studyplan_subject.subject_cat_id
                     JOIN subject ON subject.id = studyplan_subject.subject_id
                     JOIN guide_subject_type ON guide_subject_type.id = studyplan_subject.subject_type_id) a
                  WHERE a.studyplan_id = ANY (string_to_array(:studyplan_ids, \',\')::int[]) ORDER BY a.subject_cat_id, a.direction_id ',
            [
                'studyplan_ids' => implode(',', $this->studyplanIds),
            ])->queryAll();

        return $query ? ArrayHelper::index($query, null, ['studyplan_id']) : [];
    }

    /**
     * @param $model
     * @return string|void
     */
    public function getSubjects($model)
    {
        if (!isset($this->studyplanData[$model->studyplan_id])) {
            return;
        }
        $v = [];
        foreach ($this->studyplanData[$model->studyplan_id] as $studyplan_subject) {
            if (!$studyplan_subject) {
                continue;
            }
            $string = '';
            $string .= isset($this->model_date->subject_id) && $studyplan_subject['subject_id'] == $this->model_date->subject_id ? '<span style="background-color:greenyellow">' . $studyplan_subject['subject'] . '</span>' : $studyplan_subject['subject'];
            $string .= ' - ';
            $string .= isset($this->model_date->teachers_id) && $studyplan_subject['teachers_id'] == $this->model_date->teachers_id ? '<span style="background-color:yellow">' . $studyplan_subject['teacher'] . '</span>' : $studyplan_subject['teacher'];
            $v[] = $string;
        }
        return implode('<BR/> ', $v);
    }
}