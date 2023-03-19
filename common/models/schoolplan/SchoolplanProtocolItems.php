<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use common\models\education\LessonMark;
use common\models\education\LessonProgress;
use common\models\studyplan\StudyplanSubject;
use common\models\subjectsect\SubjectSect;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "schoolplan_protocol_items".
 *
 * @property int $id
 * @property int|null $schoolplan_protocol_id Протокол
 * @property int $studyplan_subject_id Учебный предмет ученика
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
 * @property SchoolplanProtocol $schoolplanProtocol
 * @property StudyplanSubject $studyplanSubject
 * @property SubjectSect $subjectSect
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
            [['schoolplan_protocol_id', 'studyplan_subject_id', 'lesson_mark_id', 'status_exe', 'status_sign', 'signer_id'], 'default', 'value' => null],
            [['schoolplan_protocol_id', 'studyplan_subject_id', 'lesson_mark_id', 'status_exe', 'status_sign', 'signer_id', 'version'], 'integer'],
            [['studyplan_subject_id', 'resume'], 'required'],
            [['resume'], 'string', 'max' => 1024],
            [['thematic_items_list'], 'safe'],
            [['winner_id'], 'string', 'max' => 255],
            [['lesson_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonMark::className(), 'targetAttribute' => ['lesson_mark_id' => 'id']],
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
            'studyplan_subject_id' => 'Учебный предмет ученика',
            'thematic_items_list' => 'Список заданий из репертуарного плана',
            'lesson_mark_id' => 'Оценка',
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
     * Gets query for [[SchoolplanProtocol]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolplanProtocol()
    {
        return $this->hasOne(SchoolplanProtocol::className(), ['id' => 'schoolplan_protocol_id']);
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
        return [
            1 => 'На подписи',
            2 => 'Подписано',
            3 => 'Не подписано',
        ];
    }

    /**
     * @return array
     */
    public static function getStatusSignOptionsList()
    {
        return [
            [1, 'На подписи', 'info'],
            [2, 'Подписано', 'success'],
            [3, 'Не подписано', 'danger']
        ];
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
}
