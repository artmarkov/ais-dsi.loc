<?php

namespace common\models\education;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\Schedule;
use common\models\subjectsect\SubjectScheduleTeachersView;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "lesson_items".
 *
 * @property int $id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int $lesson_test_id
 * @property int $lesson_date
 * @property string|null $lesson_topic
 * @property string|null $lesson_rem
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property GuideLessonTest $lessonTest
 * @property LessonProgress[] $lessonProgresses
 */
class LessonItems extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_items';
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
                'class' => DateFieldBehavior::class,
                'attributes' => ['lesson_date'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'lesson_test_id', 'version'], 'integer'],
            [['subject_sect_studyplan_id', 'studyplan_subject_id'], 'default', 'value'=> 0],
            [['lesson_test_id', 'lesson_date'], 'required'],
            [['lesson_date'], 'safe'],
            [['lesson_date'], 'checkLessonExist', 'skipOnEmpty' => false],
            [['lesson_date'], 'checkLessonDate', 'skipOnEmpty' => false],
            [['lesson_topic'], 'string', 'max' => 512],
            [['lesson_rem'], 'string', 'max' => 1024],
            [['lesson_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonTest::className(), 'targetAttribute' => ['lesson_test_id' => 'id']],
        ];
    }

    public function checkLessonExist($attribute, $params)
    {
        if ($this->isNewRecord) {
            $checkLesson = self::find()->where(
                ['AND',
                    ['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id],
                    ['=', 'studyplan_subject_id', $this->studyplan_subject_id],
                    ['=', 'lesson_date', strtotime($this->lesson_date)],

                ]);
            if ($checkLesson->exists() === true) {
                $this->addError($attribute, 'Занятие уже добавлено для выбранной даты и дисциплины!');
            }
        }
    }

    /**
     * Проверка на существование занятия
     * @param $subject_sect_studyplan_id
     * @param $studyplan_subject_id
     * @param $lesson_timestamp
     * @return bool
     */
    public static function isLessonExist($subject_sect_studyplan_id, $studyplan_subject_id, $lesson_timestamp)
    {
        return self::find()->where(
            ['AND',
                ['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id],
                ['=', 'studyplan_subject_id', $studyplan_subject_id],
                ['=', 'lesson_date', $lesson_timestamp],
            ])->exists();
    }
//    public function checkLesson()
//    {
//            $checkLesson = self::find()->where(
//                ['AND',
//                    ['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id],
//                    ['=', 'studyplan_subject_id', $this->studyplan_subject_id],
//                    ['=', 'lesson_date', strtotime($this->lesson_date)],
//
//                ]);
//           return $checkLesson->scalar();
//    }

    public function checkLessonDate($attribute, $params)
    {
        $checkLesson = SubjectScheduleTeachersView::find()->where(
            ['AND',
                ['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id],
                ['=', 'studyplan_subject_id', $this->studyplan_subject_id],
                ['=', 'week_day', Schedule::timestamp2WeekDay(strtotime($this->lesson_date))],

            ]);
        if ($checkLesson->exists() !== true) {
            $this->addError($attribute, 'Дата занятия не соответствует расписанию!');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Subject Sect'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Studyplan Subject'),
            'lesson_test_id' => Yii::t('art/guide', 'Lesson Test'),
            'lesson_date' => Yii::t('art/guide', 'Lesson Date'),
            'lesson_topic' => Yii::t('art/guide', 'Lesson Topic'),
            'lesson_rem' => Yii::t('art/guide', 'Lesson Rem'),
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
     * Gets query for [[LessonTest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonTest()
    {
        return $this->hasOne(LessonTest::className(), ['id' => 'lesson_test_id']);
    }

    /**
     * Gets query for [[LessonProgresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonProgresses()
    {
        return $this->hasMany(LessonProgress::className(), ['lesson_items_id' => 'id'])->innerJoin('subject_sect_studyplan', 'subject_sect_studyplan.id = lesson_items.subject_sect_studyplan_id');
    }

}
