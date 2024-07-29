<?php

namespace common\models\education;

use artsoft\helpers\RefBook;
use artsoft\widgets\Notice;
use common\models\studyplan\StudyplanSubject;
use phpDocumentor\Reflection\Types\False_;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "lesson_progress".
 *
 * @property int $id
 * @property int|null $lesson_items_id
 * @property int|null $studyplan_subject_id
 * @property int $lesson_mark_id
 * @property string|null $mark_rem
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property LessonItems $lessonItems
 * @property StudyplanSubject $studyplanSubject
 */
class LessonProgress extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_progress';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lesson_items_id', 'studyplan_subject_id', 'lesson_mark_id', 'version'], 'integer'],
            [['studyplan_subject_id'/*, 'lesson_mark_id'*/], 'required'],
//            [['studyplan_subject_id'], 'checkProgressExist'],
            [['mark_rem'], 'string', 'max' => 127],
            [['lesson_items_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonItems::className(), 'targetAttribute' => ['lesson_items_id' => 'id']],
            [['lesson_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['lesson_mark_id' => 'id']],
            [['studyplan_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyplanSubject::className(), 'targetAttribute' => ['studyplan_subject_id' => 'id']],
        ];
    }

    public function checkProgressExist($attribute, $params)
    {
        $checkLesson = self::find()->where(
            ['AND',
                ['=', 'lesson_items_id', $this->lesson_items_id],
                ['=', 'studyplan_subject_id', $this->studyplan_subject_id],

            ]);
        if ($checkLesson->exists() === true) {
            $message = 'Ученику можно ставить только одну оценку за урок';
            // $this->addError($attribute, $message);
            Notice::registerWarning($message);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'lesson_items_id' => Yii::t('art/guide', 'Lesson Items'),
            'studyplan_subject_id' => Yii::t('art/student', 'Student'),
            'lesson_mark_id' => Yii::t('art/guide', 'Mark'),
            'mark_rem' => Yii::t('art/guide', 'Mark Rem'),
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
     * Gets query for [[LessonItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonItems()
    {
        return $this->hasOne(LessonItems::className(), ['id' => 'lesson_items_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLessonMark()
    {
        return $this->hasOne(LessonMark::className(), ['id' => 'lesson_mark_id']);
    }

    /**
     * Gets query for [[StudyplanSubject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanSubject()
    {
        return $this->hasOne(StudyplanSubject::className(), ['id' => 'studyplan_subject_id']);
    }

    public static function getStudentName($studyplan_subject_id)
    {
        $student_id = RefBook::find('studyplan_subject-student')->getValue($studyplan_subject_id);
        return RefBook::find('students_fio')->getValue($student_id);
    }

}
