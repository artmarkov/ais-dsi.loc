<?php

namespace common\models\schedule;

use artsoft\helpers\Schedule;
use artsoft\widgets\Tooltip;
use common\models\guidejob\Direction;
use Yii;

/**
 * This is the model class for table "consult_schedule_view".
 *
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_list
 * @property float|null $year_time_consult,
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
 * @property string|null $sect_name
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
        $attr['year_time_consult'] = Yii::t('art/guide', 'Year Time Consult');
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
        $attr['sect_name'] = Yii::t('art/guide', 'Sect Name');
        $attr['subject'] = Yii::t('art/guide', 'Subject');

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


    /**
     * В одной аудитории накладка по времени консультаций!
     * @param $model
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersConsultOverLapping()
    {
        if ($this->consult_schedule_id == NULL) {
            return null;
        }
        $thereIsAnOverlapping = self::find()->where(
            ['AND',
                ['!=', 'consult_schedule_id', $this->consult_schedule_id],
                ['auditory_id' => $this->auditory_id],
                ['plan_year' => $this->plan_year],
                ['OR',
                    ['AND',
                        ['<', 'datetime_in', \Yii::$app->formatter->asTimestamp($this->datetime_out)],
                        ['>=', 'datetime_in', \Yii::$app->formatter->asTimestamp($this->datetime_in)],
                    ],

                    ['AND',
                        ['<=', 'datetime_out', \Yii::$app->formatter->asTimestamp($this->datetime_out)],
                        ['>', 'datetime_out', \Yii::$app->formatter->asTimestamp($this->datetime_in)],
                    ],
                ],
            ])->exists();

        return $thereIsAnOverlapping;
    }

    /**
     * Преподаватель не может работать в одно и тоже время в разных аудиториях (консультации)!
     * @param $this
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersOverLapping()
    {
        if ($this->consult_schedule_id == NULL) {
            return null;
        }
        $thereIsAnOverlapping = self::find()->where(
            ['AND',
                ['!=', 'consult_schedule_id', $this->consult_schedule_id],
                ['direction_id' => $this->direction_id],
                ['teachers_id' => $this->teachers_id],
                ['!=', 'auditory_id', $this->auditory_id],
                ['plan_year' => $this->plan_year],
                ['OR',
                    ['AND',
                        ['<', 'datetime_in', \Yii::$app->formatter->asTimestamp($this->datetime_out)],
                        ['>=', 'datetime_in', \Yii::$app->formatter->asTimestamp($this->datetime_in)],
                    ],

                    ['AND',
                        ['<=', 'datetime_out', \Yii::$app->formatter->asTimestamp($this->datetime_out)],
                        ['>', 'datetime_out', \Yii::$app->formatter->asTimestamp($this->datetime_in)],
                    ],
                ],
            ])->exists();

        return $thereIsAnOverlapping;
    }

    public function getConsultOverLappingNotice()
    {
        $tooltip = [];
        if ($this->getTeachersConsultOverLapping()) {
            $message = 'В одной аудитории накладка по времени консультации';
            $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
        }

        if ($this->getTeachersOverLapping()) {
            $message = 'Преподаватель не может в одно и то же время проводить консультации в разных аудиториях.';
            $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
        }
        return implode('', $tooltip);
    }

    public static function getTotal($provider, $fieldName)
    {
        if ($fieldName == 'load_time_consult' || $fieldName == 'year_time_consult') {
            $total = [];
            foreach ($provider as $item) {
                if (!isset($total[$item['teachers_load_id']])) {
                    $total[$item['teachers_load_id']] = $item[$fieldName];
                }
            }
            return array_sum($total);
        } elseif ($fieldName == 'datetime_in') {
            $total = 0;
            foreach ($provider as $item) {
                if (isset($item['datetime_in']) && isset($item['datetime_out'])) {
                    $total += Schedule::astr2academ(\Yii::$app->formatter->asTimestamp($item['datetime_out']) - \Yii::$app->formatter->asTimestamp($item['datetime_in']));
                }
            }
            return $total;
        }
    }

}
