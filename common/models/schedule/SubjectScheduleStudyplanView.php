<?php

namespace common\models\schedule;


use artsoft\helpers\ArtHelper;
use common\models\studyplan\Studyplan;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "subject_schedule_studyplan_view".
 *
 * @property int|null studyplan_subject_id
 * @property float|null week_time
 * @property int|null subject_sect_studyplan_id
 * @property int|null studyplan_subject_list
 * @property int|null subject_type_id
 * @property int|null subject_sect_id
 * @property int|null studyplan_id
 * @property int|null student_id
 * @property int|null plan_year
 * @property int|null status
 * @property int|null teachers_load_id
 * @property int|null direction_id
 * @property int|null teachers_id
 * @property int|null load_time
 * @property int|null subject_schedule_id
 * @property int|null week_num
 * @property int|null week_day
 * @property int|null time_in
 * @property int|null time_out
 * @property int|null auditory_id
 * @property string|null description
 */
class SubjectScheduleStudyplanView extends SubjectScheduleView
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_schedule_studyplan_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['studyplan_id'] = Yii::t('art/guide', 'Studyplan');
        $attr['student_id'] = Yii::t('art/student', 'Student');
        $attr['status'] = Yii::t('art', 'Status');

        return $attr;
    }

    /**
     * @param $subject_key
     * @param $timestamp_in
     * @return array
     */
    public static function getScheduleIndiv($subject_key, $teachers_id, $timestamp_in)
    {
        return (new Query())->select(['week_num', 'week_day', 'time_in', 'time_out', 'auditory_id', 'student_fio'])
            ->distinct()
            ->from('subject_schedule_studyplan_view')
            ->innerJoin('guide_teachers_direction', 'guide_teachers_direction.id = subject_schedule_studyplan_view.direction_id')
            ->where(
                ['AND',
                    ['=', 'subject_key', $subject_key],
                    ['=', 'teachers_id', $teachers_id],
                    ['=', 'plan_year', ArtHelper::getStudyYearDefault(null, $timestamp_in)]
                ])
            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
            ->andWhere(['is', 'guide_teachers_direction.parent', null])
            ->all();
    }
}
