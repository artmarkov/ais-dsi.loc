<?php

namespace common\models\schedule;

use common\models\guidejob\Direction;
use Yii;

/**
 * This is the model class for table "consult_schedule_view".
 *
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_list
 * @property string|null $subject_type_id
 * @property int|null $subject_sect_id
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $teachers_load_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property float|null $load_time_consult
 * @property int|null $consult_schedule_id
 * @property int|null $datetime_in
 * @property int|null $datetime_out
 * @property string|null $auditory_id
 * @property string|null $description
 */

class ConsultScheduleView extends ConsultSchedule
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consult_schedule_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['studyplan_subject_id'] = Yii::t('art/guide', 'Subject Name');
        $attr['subject_sect_studyplan_id'] = Yii::t('art/guide', 'Sect Name');
        $attr['studyplan_subject_list'] = Yii::t('art/guide', 'Studyplan List');
        $attr['subject_type_id'] = Yii::t('art/guide', 'Subject Type ID');
        $attr['subject_sect_id'] = Yii::t('art/guide', 'Subject Sect ID');
        $attr['studyplan_id'] = Yii::t('art/guide', 'Studyplan');
        $attr['student_id'] = Yii::t('art/student', 'Student');
        $attr['plan_year'] = Yii::t('art/studyplan', 'Plan Year');
        $attr['status'] = Yii::t('art', 'Status');
        $attr['direction_id'] = Yii::t('art/teachers', 'Name Direction');
        $attr['teachers_id'] = Yii::t('art/teachers', 'Teachers');
        $attr['load_time_consult'] = Yii::t('art/guide', 'Load Time Consult');
        $attr['consult_schedule_id'] = Yii::t('art/guide', 'Consult Schedule ID');

        return $attr;
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

}
