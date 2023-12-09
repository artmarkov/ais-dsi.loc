<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use artsoft\models\User;
use common\models\education\LessonMark;
use common\models\education\LessonProgress;
use common\models\studyplan\StudyplanSubject;
use common\models\subjectsect\SubjectSect;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "schoolplan_perform".
 *
 * @property int $id
 * @property int|null $schoolplan_id Мероприятие
 * @property int|null $studyplan_id Учебный план
 * @property int $studyplan_subject_id Учебный предмет плана ученика
 * @property int $teachers_id Преподаватель
 * @property string|null $thematic_items_list Список заданий из тематич/реп плана
 * @property int $lesson_mark_id Оцкнка
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
 * @property lessonMark $lessonMark
 * @property Schoolplan $schoolplan
 * @property StudyplanSubject $studyplanSubject
 * @property Teachers $teachers
 * @property User $user
 */
class SchoolplanPerform extends \artsoft\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan_perform';
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
            [['lesson_mark_id', 'status_exe', 'status_sign', 'signer_id'], 'default', 'value' => null],
            [['schoolplan_id', 'studyplan_id', 'studyplan_subject_id', 'teachers_id', 'lesson_mark_id', 'status_exe', 'status_sign', 'signer_id', 'version'], 'integer'],
            [['teachers_id', 'signer_id', 'status_exe'], 'required'],
            [['resume'], 'string', 'max' => 1024],
            [['thematic_items_list'], 'safe'],
            [['winner_id'], 'string', 'max' => 255],
            [['lesson_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['lesson_mark_id' => 'id']],
            [['schoolplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schoolplan::className(), 'targetAttribute' => ['schoolplan_id' => 'id']],
            [['studyplan_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyplanSubject::className(), 'targetAttribute' => ['studyplan_subject_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_id' => 'id']],
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
            'winner_id' => 'Звание/Диплом',
            'resume' => 'Отзыв комиссии/Результат',
            'status_exe' => 'Статус выполнения',
            'status_sign' => 'Статус документа',
            'signer_id' => 'Подписант',
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'mark_flag' => 'Добавить оценку',
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

    /**
     * Gets query for [[Schoolplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolplan()
    {
        return $this->hasOne(Schoolplan::className(), ['id' => 'schoolplan_id']);
    }

    public function getSchoolplanProtocols()
    {
        $schoolplan_id = $this->schoolplanProtocol->schoolplan_id ?? null;
        return ArrayHelper::map(SchoolplanProtocol::find()->select(['id', 'protocol_name'])->andWhere(['=', 'schoolplan_id', $schoolplan_id])->asArray()->all(), 'id', 'protocol_name');
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
     * @return array
     */
    public static function getStatusExeList()
    {
        return [
            1 => 'В работе',
            2 => 'Выполнено',
            3 => 'Не выполнено',
        ];
    }

    /**
     * @return array
     */
    public static function getStatusExeOptionsList()
    {
        return [
            [1, 'В работе', 'info'],
            [2, 'Выполнено', 'success'],
            [3, 'Не выполнено', 'danger']
        ];
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getStatusExeValue($val)
    {
        $ar = self::getStatusExeList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return array
     */
    public static function getStatusSignList()
    {
        return array(
            self::DOC_STATUS_DRAFT => Yii::t('art', 'Draft'),
            self::DOC_STATUS_AGREED => Yii::t('art', 'Agreed'),
            self::DOC_STATUS_WAIT => Yii::t('art', 'Wait'),
        );
    }

    /**
     * @return array
     */
    public static function getStatusSignOptionsList()
    {
        return array(
            [self::DOC_STATUS_DRAFT, Yii::t('art', 'Draft'), 'default'],
            [self::DOC_STATUS_AGREED, Yii::t('art', 'Agreed'), 'success'],
            [self::DOC_STATUS_WAIT, Yii::t('art', 'Wait'), 'warning'],
        );
    }


    /**
     * @param $val
     * @return mixed
     */
    public static function getStatusSignValue($val)
    {
        $ar = self::getStatusSignList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return array
     */
    public static function getWinnerList()
    {
        return [
            1 => 'Гран-при',
            2 => 'Лауреат I-й степени',
            3 => 'Лауреат II-й степени',
            4 => 'Лауреат III-й степени',
            5 => 'Дипломант',
            6 => 'Грамота участника',
            7 => 'Победитель',
            8 => 'Лауреат',
            9 => 'Благодарственное письмо',
        ];
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getWinnerValue($val)
    {
        $ar = self::getWinnerList();

        return isset($ar[$val]) ? $ar[$val] : $val;
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
		                   concat(studyplan_thematic_items.author , \' - \', studyplan_thematic_items.piece_name, \' (\', guide_piece_category.name, \')\') AS name
                    FROM studyplan_thematic_view 
                    INNER JOIN studyplan_thematic_items ON studyplan_thematic_view.studyplan_thematic_id = studyplan_thematic_items.studyplan_thematic_id 
                    INNER JOIN guide_piece_category ON guide_piece_category.id = studyplan_thematic_items.piece_category_id
                    where thematic_category = 2 and studyplan_subject_id = :studyplan_subject_id',
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
        return !($userId == $this->leader_id || $userId == $this->secretary_id || in_array($userId, $this->members_list));
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'signer_id']);
    }
}
