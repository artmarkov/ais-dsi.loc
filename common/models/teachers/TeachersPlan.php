<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use artsoft\widgets\Tooltip;
use common\models\guidejob\Direction;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\behaviors\TimeFieldBehavior;
use Yii;

/**
 * This is the model class for table "teachers_plan".
 *
 * @property int $id
 * @property int $direction_id
 * @property int $teachers_id
 * @property int|null $plan_year
 * @property int $half_year
 * @property int|null $week_num
 * @property int|null $week_day
 * @property int|null $time_plan_in
 * @property int|null $time_plan_out
 * @property int|null $auditory_id
 * @property string $description
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property GuideTeachersDirection $direction
 * @property Teachers $teachers
 */
class TeachersPlan extends \artsoft\db\ActiveRecord
{
    public $planDisplay;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_plan';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => TimeFieldBehavior::class,
                'attributes' => ['time_plan_in', 'time_plan_out'],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direction_id', 'teachers_id', 'time_plan_in', 'time_plan_out', 'plan_year', 'week_day', 'auditory_id'], 'required'],
            [['description'], 'string', 'max' => 512],
            [['half_year', 'week_num'], 'default', 'value' => 0],
            [['time_plan_in', 'time_plan_out'], 'safe'],
            [['direction_id', 'teachers_id', 'plan_year', 'week_num', 'week_day', 'auditory_id', 'half_year'], 'integer'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
            [['time_plan_in', 'time_plan_out'], 'checkFormatTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['time_plan_out'], 'compare', 'compareAttribute' => 'time_plan_in', 'operator' => '>', 'message' => 'Время окончания не может быть меньше или равно времени начала.'],

        ];
    }

    public function checkFormatTime($attribute, $params)
    {
        if (!preg_match('/^([01]?[0-9]|2[0-3])(:|\.)[0-5][0-9]$/', $this->$attribute)) {
            $this->addError($attribute, 'Формат ввода времени не верен.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teacher'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'half_year' => Yii::t('art/guide', 'Half Year'),
            'week_num' => Yii::t('art/guide', 'Week Num'),
            'week_day' => Yii::t('art/guide', 'Week Day'),
            'time_plan_in' => Yii::t('art/guide', 'Time In'),
            'time_plan_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory'),
            'description' => Yii::t('art', 'Description'),
            'planDisplay' => Yii::t('art/guide', 'Plan Schedule'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
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
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'teachers_id']);
    }

    /**
     * @return string
     */
    public function getPlanDisplay()
    {
        $string = ' ' . ArtHelper::getWeekValue('short', $this->week_num);
        $string .= ' ' . ArtHelper::getWeekdayValue('short', $this->week_day) . ' ' . $this->time_plan_in . '-' . $this->time_plan_out;
         $string .= ' ' . $this->getTeachersPlanNotice();
        return $this->time_plan_in ? $string : null;
    }

    public function getPlanTimeDisplay()
    {
        $string =  $this->time_plan_in . '-' . $this->time_plan_out;
        $string .= ' ' . $this->getTeachersPlanNotice();
        return $this->time_plan_in ? $string : null;
    }

    /**
     * В одной аудитории накладка по времени!
     * @param $model
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersPlanOverLapping()
    {
        $thereIsAnOverlapping = self::find()->where(
            ['AND',
                ['!=', 'id', $this->id],
                ['auditory_id' => $this->auditory_id],
                ['direction_id' => $this->direction_id],
                ['plan_year' => $this->plan_year],
                ['half_year' => $this->half_year],
                ['OR',
                    ['AND',
                        ['<', 'time_plan_in', Schedule::encodeTime($this->time_plan_out)],
                        ['>=', 'time_plan_in', Schedule::encodeTime($this->time_plan_in)],
                    ],

                    ['AND',
                        ['<=', 'time_plan_out', Schedule::encodeTime($this->time_plan_out)],
                        ['>', 'time_plan_out', Schedule::encodeTime($this->time_plan_in)],
                    ],
                ],
                ['=', 'week_day', $this->week_day]
            ]);
        if ($this->getAttribute($this->week_num) !== 0) {
            $thereIsAnOverlapping->andWhere(['=', 'week_num', $this->week_num]);
        }

        return $thereIsAnOverlapping;
    }

    /**
     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
     * @param $this
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersOverLapping()
    {
        $thereIsAnOverlapping = self::find()->where(
            ['AND',
                ['!=', 'id', $this->id],
                ['direction_id' => $this->direction_id],
                ['teachers_id' => $this->teachers_id],
                ['!=', 'auditory_id', $this->auditory_id],
                ['plan_year' => $this->plan_year],
                ['half_year' => $this->half_year],
                ['OR',
                    ['AND',
                        ['<', 'time_plan_in', Schedule::encodeTime($this->time_plan_out)],
                        ['>=', 'time_plan_in', Schedule::encodeTime($this->time_plan_in)],
                    ],

                    ['AND',
                        ['<=', 'time_plan_out', Schedule::encodeTime($this->time_plan_out)],
                        ['>', 'time_plan_out', Schedule::encodeTime($this->time_plan_in)],
                    ],
                ],
                ['=', 'week_day', $this->week_day]
            ]);
        if ($this->getAttribute($this->week_num) !== 0) {
            $thereIsAnOverlapping->andWhere(['=', 'week_num', $this->week_num]);
        }

        return $thereIsAnOverlapping;
    }

    public function getTeachersPlanNotice()
    {
        $tooltip = [];
            if ($this->getTeachersPlanOverLapping()->exists() === true) {
                $info = [];
                foreach (self::getTeachersPlanOverLapping()->all() as $itemModel) {
                    $info[] = RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id) . ' - ' . RefBook::find('teachers_fio')->getValue($itemModel->teachers_id);
                }
                $message = 'В одной аудитории при планировании накладка по времени! ' . implode(', ', $info);
                //  Notice::registerDanger($message);
                $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
            }

            if ($this->getTeachersOverLapping()->exists() === true) {
                $info = [];
                foreach ($this->getTeachersOverLapping()->all() as $itemModel) {
                    $info[] = RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
                }
                $message = 'Преподаватель(концертмейстер) не может работать в одно и тоже время в разных аудиториях! ' . implode(', ', $info);
                //   Notice::registerDanger($message);
                $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
            }
            return !empty($tooltip) ? implode('', $tooltip) : null;
    }
}
