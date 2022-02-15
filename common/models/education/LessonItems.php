<?php

namespace common\models\education;

use Yii;
use artsoft\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

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
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'lesson_test_id', 'lesson_date', 'version'], 'integer'],
            [['lesson_test_id', 'lesson_date'], 'required'],
            [['lesson_topic'], 'string', 'max' => 512],
            [['lesson_rem'], 'string', 'max' => 1024],
            [['lesson_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonTest::className(), 'targetAttribute' => ['lesson_test_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
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
            'created_at' => Yii::t('art/guide', 'Created'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
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
        return $this->hasOne(GuideLessonTest::className(), ['id' => 'lesson_test_id']);
    }

    /**
     * Gets query for [[LessonProgresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonProgresses()
    {
        return $this->hasMany(LessonProgress::className(), ['lesson_items_id' => 'id']);
    }
}
