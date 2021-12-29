<?php

namespace common\models\views;

use common\models\auditory\Auditory;
use common\models\guidejob\Direction;
use common\models\studyplan\StudyplanSubject;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\teachers\Teachers;
use Yii;

/**
 * This is the model class for table "subject_sect_schedule_view".
 *
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property float|null $week_time
 * @property int|null $subject_sect_id
 * @property string|null $studyplan_subject_list
 * @property int|null $plan_year
 * @property int|null $subject_sect_schedule_id
 * @property int|null $week_num
 * @property int|null $week_day
 * @property int|null $time_in
 * @property int|null $time_out
 * @property int|null $auditory_id
 * @property string|null $description
 */
class SubjectSectScheduleView extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect_schedule_view';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_sect_studyplan_id', 'direction_id', 'teachers_id', 'subject_sect_id', 'plan_year', 'subject_sect_schedule_id', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id'], 'default', 'value' => null],
            [['subject_sect_studyplan_id', 'direction_id', 'teachers_id', 'subject_sect_id', 'plan_year', 'subject_sect_schedule_id', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id'], 'integer'],
            [['week_time'], 'number'],
            [['studyplan_subject_list'], 'string'],
            [['description'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Subject Sect Studyplan ID'),
            'direction_id' => Yii::t('art/guide', 'Direction ID'),
            'teachers_id' => Yii::t('art/guide', 'Teachers ID'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect ID'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan Subject List'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'subject_sect_schedule_id' => Yii::t('art/guide', 'Subject Sect Schedule ID'),
            'week_num' => Yii::t('art/guide', 'Week Num'),
            'week_day' => Yii::t('art/guide', 'Week Day'),
            'time_in' => Yii::t('art/guide', 'Time In'),
            'time_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory ID'),
            'description' => Yii::t('art/guide', 'Description'),
        ];
    }
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }
    /**
     * Gets query for [[SubjectSectStudyplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSectStudyplan()
    {
        return $this->hasOne(SubjectSectStudyplan::class, ['id' => 'subject_sect_studyplan_id']);
    }

    public function getStudyplanSubject()
    {
        return $this->hasOne(StudyplanSubject::className(), ['id' => 'studyplan_subject_id']);
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'teachers_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::class, ['id' => 'teachers_id']);
    }
}
