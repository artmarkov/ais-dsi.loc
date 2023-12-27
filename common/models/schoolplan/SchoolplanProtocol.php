<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\education\LessonMark;
use common\models\education\LessonProgress;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subjectsect\SubjectSect;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "schoolplan_protocol".
 *
 * @property int $id
 * @property int|null $schoolplan_id Мероприятие
 * @property int|null $studyplan_id Учебный план
 * @property int $studyplan_subject_id Учебный предмет плана ученика
 * @property int $teachers_id Преподаватель
 * @property string|null $thematic_items_list Список заданий из тематич/реп плана
 * @property int $lesson_mark_id Оцкнка
 * @property string $resume Отзыв комиссии/Результат
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property lessonMark $lessonMark
 * @property Schoolplan $schoolplan
 * @property StudyplanSubject $studyplanSubject
 * @property Teachers $teachers
 * @property User $user
 */
class SchoolplanProtocol extends \artsoft\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan_protocol';
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
                'attributes' => ['thematic_items_list'],
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
            [['lesson_mark_id'], 'default', 'value' => null],
            [['schoolplan_id', 'studyplan_id', 'studyplan_subject_id', 'teachers_id', 'lesson_mark_id', 'version'], 'integer'],
            [['teachers_id', 'studyplan_id', 'studyplan_subject_id', 'thematic_items_list'], 'required'],
            [['resume'], 'string', 'max' => 1024],
            [['thematic_items_list'], 'safe'],
            [['lesson_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['lesson_mark_id' => 'id']],
            [['schoolplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schoolplan::className(), 'targetAttribute' => ['schoolplan_id' => 'id']],
            [['studyplan_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyplanSubject::className(), 'targetAttribute' => ['studyplan_subject_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_id' => 'id']],
            [['studyplan_id'], 'unique', 'targetAttribute' => ['schoolplan_id', 'studyplan_subject_id', 'teachers_id'], 'message' => 'Ученик уже записан в протокол.'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolplan_id' => 'Мероприятие',
            'studyplan_id' => 'Ученик',
            'studyplan_subject_id' => 'Учебный предмет',
            'teachers_id' => 'Преподаватель',
            'thematic_items_list' => 'Список заданий из репертуарного плана',
            'lesson_mark_id' => 'Оценка',
            'resume' => 'Отзыв комиссии/Результат',
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
     * Gets query for [[LessonMark]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonMark()
    {
        return $this->hasOne(LessonMark::className(), ['id' => 'lesson_mark_id']);
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::className(), ['id' => 'teachers_id']);
    }

    public function isAuthor()
    {
        $userId = Yii::$app->user->identity->getId();
        return $this->teachers_id == RefBook::find('users_teachers')->getValue($userId);
    }

    /**
     * Gets query for [[Schoolplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolplan()
    {
        return $this->hasOne(Schoolplan::className(), ['id' => 'schoolplan_id']);
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

    public function getStudyplan()
    {
        return $this->hasOne(Studyplan::className(), ['id' => 'studyplan_id']);
    }

    /**
     * Нахождение всех элементов репертуарного плана для $studyplan_subject_id
     * @param $studyplan_subject_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getStudyplanThematicItemsById($studyplan_subject_id)
    {
        return Yii::$app->db->createCommand(' select studyplan_thematic_items.id as id,
		                  studyplan_thematic_items.topic AS name
                    FROM studyplan_thematic_view 
                    INNER JOIN studyplan_thematic_items ON studyplan_thematic_view.studyplan_thematic_id = studyplan_thematic_items.studyplan_thematic_id 
                    where  studyplan_subject_id = :studyplan_subject_id AND studyplan_thematic_items.topic != \'\'',
            ['studyplan_subject_id' => $studyplan_subject_id,
            ])->queryAll();
    }

    public static function getThematicItemsByStudyplanSubject($studyplan_subject_id)
    {
        return ArrayHelper::map(self::getStudyplanThematicItemsById($studyplan_subject_id), 'id', 'name');
    }

    /**
     * доступ к протоколу для заполнения
     * @return bool
     */
    public function protocolIsAvailable()
    {
        $userId = Yii::$app->user->identity->getId();
        return !($userId == $this->schoolplan->protocol_leader_id || $userId == $this->schoolplan->protocol_secretary_id || in_array($userId, $this->schoolplan->protocol_members_list) || in_array($userId, $this->schoolplan->executors_list));
    }
}
