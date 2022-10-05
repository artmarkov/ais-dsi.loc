<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use common\models\education\LessonProgress;
use common\models\studyplan\StudyplanSubject;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "schoolplan_protocol_items".
 *
 * @property int $id
 * @property int|null $schoolplan_protocol_id Протокол
 * @property int $studyplan_subject_id Дисциплина ученика
 * @property string|null $thematic_items_list Список заданий из тематич/реп плана
 * @property int $lesson_progress_id Связь с уроком и оценкой
 * @property string|null $winner_id Звание/Диплом
 * @property string $resume Отзыв комиссии/Результат
 * @property int|null $status_exe Статус выполнения
 * @property int|null $status_sign Статус утверждения
 * @property int|null $signer_id Подписант
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property LessonProgress $lessonProgress
 * @property SchoolplanProtocol $schoolplanProtocol
 * @property StudyplanSubject $studyplanSubject
 */
class SchoolplanProtocolItems extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan_protocol_items';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['thematic_items_list', 'executors_list'],
            ],
            [
                'class' => FileManagerBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['schoolplan_protocol_id', 'studyplan_subject_id', 'lesson_progress_id', 'status_exe', 'status_sign', 'signer_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'default', 'value' => null],
            [['schoolplan_protocol_id', 'studyplan_subject_id', 'lesson_progress_id', 'status_exe', 'status_sign', 'signer_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['studyplan_subject_id', 'lesson_progress_id', 'resume'], 'required'],
            [['thematic_items_list', 'resume'], 'string', 'max' => 1024],
            [['winner_id'], 'string', 'max' => 255],
            [['lesson_progress_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonProgress::className(), 'targetAttribute' => ['lesson_progress_id' => 'id']],
            [['schoolplan_protocol_id'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolplanProtocol::className(), 'targetAttribute' => ['schoolplan_protocol_id' => 'id']],
            [['studyplan_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyplanSubject::className(), 'targetAttribute' => ['studyplan_subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolplan_protocol_id' => 'Протокол',
            'studyplan_subject_id' => 'Дисциплина ученика',
            'thematic_items_list' => 'Список заданий из репертуарного плана',
            'lesson_progress_id' => 'Связь с уроком и оценкой',
            'winner_id' => 'Звание/Диплом',
            'resume' => 'Отзыв комиссии/Результат',
            'status_exe' => 'Статус выполнения',
            'status_sign' => 'Статус утверждения',
            'signer_id' => 'Подписант',
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[LessonProgress]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonProgress()
    {
        return $this->hasOne(LessonProgress::className(), ['id' => 'lesson_progress_id']);
    }

    /**
     * Gets query for [[SchoolplanProtocol]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolplanProtocol()
    {
        return $this->hasOne(SchoolplanProtocol::className(), ['id' => 'schoolplan_protocol_id']);
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
