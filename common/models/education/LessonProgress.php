<?php

namespace common\models\education;

use common\models\studyplan\StudyplanSubject;
use artsoft\models\User;
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
            [['lesson_mark_id'], 'required'],
            [['lesson_items_id', 'studyplan_subject_id'], 'unique', 'targetAttribute' => ['lesson_items_id', 'studyplan_subject_id']],
            [['mark_rem'], 'string', 'max' => 127],
            [['lesson_items_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonItems::className(), 'targetAttribute' => ['lesson_items_id' => 'id']],
            [['lesson_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['lesson_mark_id' => 'id']],
            [['studyplan_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyplanSubject::className(), 'targetAttribute' => ['studyplan_subject_id' => 'id']],
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
            'lesson_items_id' => Yii::t('art/guide', 'Lesson Items'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Studyplan Subject'),
            'lesson_mark_id' => Yii::t('art/guide', 'Mark'),
            'mark_rem' => Yii::t('art/guide', 'Mark Rem'),
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

}
