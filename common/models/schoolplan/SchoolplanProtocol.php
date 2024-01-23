<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\Notice;
use common\models\education\LessonMark;
use common\models\education\LessonProgress;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subjectsect\SubjectSect;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersLoadStudyplanView;
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
 * @property int $studyplan_subject_id Учебный предмет плана ученика
 * @property int $teachers_id Преподаватель
 * @property string|null $thematic_items_list Список заданий из тематич/реп плана
 * @property string|null $task_ticket
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

    public $thematicFlag;

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
            [['schoolplan_id', 'teachers_id', 'lesson_mark_id', 'version'], 'integer'],
            [['teachers_id', 'studyplan_subject_id'], 'required'],
            [['resume'], 'string', 'max' => 1024],
            [['task_ticket'], 'string', 'max' => 127],
            [['thematic_items_list', 'studyplan_subject_id'], 'safe'],
            [['thematicFlag'], 'boolean'],
            [['lesson_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['lesson_mark_id' => 'id']],
            [['schoolplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schoolplan::className(), 'targetAttribute' => ['schoolplan_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_id' => 'id']],
            [['studyplan_subject_id'], 'isUniqueProtocolItem', 'skipOnEmpty' => false],
        ];
    }

    public function isUniqueProtocolItem($attribute, $params, $validator)
    {
        $exists = self::find()
            ->select('studyplan_subject_id')
            ->where(['schoolplan_id' => $this->schoolplan_id])
            ->andWhere(['studyplan_subject_id' => $this->studyplan_subject_id])
            ->andWhere(['teachers_id' => $this->teachers_id]);
        if (!$this->isNewRecord) {
            $exists = $exists->andWhere(['!=', 'id', $this->id]);
        }
        $exists = $exists->column();

        if ($exists) {
            $m = [];
            foreach ($exists as $item => $studyplan_subject_id) {
                $m[] = RefBook::find('subject_memo_4')->getValue($studyplan_subject_id);
            }
            $message = 'Ученик уже добавлен в протокол.';
            $this->addError($attribute, $message);
            if (!empty($m)) {
                $message .= ' ' . implode(', ', $m);
                Notice::registerDanger($message);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolplan_id' => 'Мероприятие',
            'studyplan_subject_id' => 'Ученик',
            'teachers_id' => 'Преподаватель',
            'thematic_items_list' => 'Список заданий из репертуарного плана',
            'task_ticket' => 'Задание/Билет',
            'lesson_mark_id' => 'Оценка',
            'resume' => 'Отзыв комиссии/Результат',
            'thematicFlag' => 'Взять задание из репертуарного плана',
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


    /**
     * доступ к протоколу для заполнения
     * @return bool
     */
    public function protocolIsAvailable()
    {
        $userId = Yii::$app->user->identity->getId();
        return !($userId == $this->schoolplan->protocol_leader_id || $userId == $this->schoolplan->protocol_secretary_id || in_array($userId, $this->schoolplan->protocol_members_list) || in_array($userId, $this->schoolplan->executors_list));
    }

    public function afterFind()
    {
        $this->thematicFlag = $this->thematic_items_list != '' ? true : false;
        parent::afterFind();
    }

    public function beforeSave($insert)
    {
        if ($this->thematicFlag) {
            $this->task_ticket = null;
        } else {
            $this->thematic_items_list = null;
        }
        return parent::beforeSave($insert);
    }

}
