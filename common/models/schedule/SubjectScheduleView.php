<?php

namespace common\models\schedule;

use artsoft\helpers\Schedule;
use artsoft\helpers\RefBook;
use artsoft\helpers\ArtHelper;
use Yii;
use artsoft\widgets\Tooltip;

/**
 * This is the model class for table "subject_schedule_view".
 *
 * @property int|null studyplan_subject_id
 * @property int|null subject_sect_studyplan_id
 * @property int|null studyplan_subject_list
 * @property int|null subject_type_id
 * @property int|null subject_sect_id
 * @property int|null plan_year
 * @property float|null week_time
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
class SubjectScheduleView extends SubjectSchedule
{
    public $scheduleDisplay;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_schedule_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {

        $attr = parent::attributeLabels();
        $attr['studyplan_subject_id'] = Yii::t('art/guide', 'Subject Name');
        $attr['week_time'] = Yii::t('art/guide', 'Week Time');
        $attr['subject_sect_studyplan_id'] = Yii::t('art/guide', 'Sect Name');
        $attr['studyplan_subject_list'] = Yii::t('art/guide', 'Studyplan List');
        $attr['subject_sect_id'] = Yii::t('art/guide', 'Subject Sect ID');
        $attr['subject_type_id'] = Yii::t('art/guide', 'Subject Type ID');
        $attr['plan_year'] = Yii::t('art/studyplan', 'Plan Year');
        $attr['direction_id'] = Yii::t('art/teachers', 'Name Direction');
        $attr['teachers_id'] = Yii::t('art/teachers', 'Teachers');
        $attr['load_time'] = Yii::t('art/guide', 'Load Time');
        $attr['subject_schedule_id'] = Yii::t('art/guide', 'Subject Schedule');
        $attr['scheduleDisplay'] = Yii::t('art/guide', 'Subject Schedule');

        return $attr;
    }

    /**
     * В одной аудитории накладка по времени!
     * Одновременное посещение разных дисциплин недопустимо!
     * Накладка по времени занятий концертмейстера!
     * Заданное расписание не соответствует планированию индивидуальных занятий!
     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
     *
     * @return null|string
     * @throws \Exception
     */
    public function getItemScheduleNotice()
    {
        $tooltip = [];
        if ($this->subject_schedule_id) {
            $model = SubjectSchedule::findOne($this->subject_schedule_id);
            if (self::getScheduleOverLapping($model)->exists() === true) {
                $info = [];
                foreach (self::getScheduleOverLapping($model)->all() as $itemModel) {
                    $info[] = RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
                }
                $message = 'В одной аудитории накладка по времени! ' . implode(', ', $info);
                //  Notice::registerDanger($message);
                $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
            }

            if (self::getTeachersOverLapping($model)->exists() === true) {
                $info = [];
                foreach (self::getScheduleOverLapping($model)->all() as $itemModel) {
                    $info[] = RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
                }
                $message = 'Преподаватель(концертмейстер) не может работать в одно и тоже время в разных аудиториях! ' . implode(', ', $info);
                //   Notice::registerDanger($message);
                $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
            }
            return implode('', $tooltip);
        }
        return null;
    }

    /**
     * Проверка на необходимость добавления расписания
     * @return bool
     */
    public function getTeachersScheduleNeed()
    {
        $delta_time = Yii::$app->settings->get('module.student_delta_time');
        $thereIsAnOverload = $this->getTeachersOverLoad();
        $weekTime = Schedule::academ2astr($this->load_time);
        if ($this->load_time != 0 && $weekTime > $thereIsAnOverload['full_time'] && abs(($weekTime - $thereIsAnOverload['full_time'])) > ($delta_time * $thereIsAnOverload['qty'])) {
            return true;
        }
        return false;
    }

    /**
     * Проверка на суммарное время расписания = времени нагрузки
     * $delta_time - погрешность, в зависимости от кол-ва занятий
     * @return string|null
     * @throws \Exception
     */
    public function getTeachersOverLoadNotice()
    {
        $message = null;
        $delta_time = Yii::$app->settings->get('module.student_delta_time');
        $thereIsAnOverload = $this->getTeachersOverLoad();
        $weekTime = Schedule::academ2astr($this->load_time);
        if ($this->load_time != 0 && $thereIsAnOverload['full_time'] != null && abs(($weekTime - $thereIsAnOverload['full_time'])) > ($delta_time * $thereIsAnOverload['qty'])) {
            $message = 'Суммарное время в расписании занятий не соответствует нагрузке!';
        }
        return $message ? Tooltip::widget(['type' => 'warning', 'message' => $message]) : null;
    }

    public static function getScheduleIsExist($subject_sect_studyplan_id, $studyplan_subject_id)
    {
        if ($subject_sect_studyplan_id == 0) {
            return self::find()->where(['=', 'studyplan_subject_id', $studyplan_subject_id])->exists();

        }
        return self::find()->where(['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id])->exists();
    }

    /**
     * @return string
     */
    public function getScheduleDisplay()
    {
        $string = ' ' . ArtHelper::getWeekValue('short', $this->week_num);
        $string .= ' ' . ArtHelper::getWeekdayValue('short', $this->week_day) . ' ' . $this->time_in . '-' . $this->time_out;
        $string .= ' ' . $this->getTeachersOverLoadNotice();
        return $this->time_in ? $string : null;
    }

}
