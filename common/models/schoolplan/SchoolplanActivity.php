<?php

namespace common\models\schoolplan;

use artsoft\Art;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;
use yii\db\Query;

/**
 * This is the model class for table "schoolplan_activity".
 *
 * @property int $id
 * @property int $schoolplan_id
 * @property int $author_id Автор работы
 * @property int $executor_id Исполнитель работы
 * @property int $datetime_in Дата и время работы
 * @property string $name Название работы
 * @property string|null $places Место работы
 * @property string|null $author_comment Описание работы
 * @property string|null $executor_comment Отчет исполнителя работы
 * @property int $activity_status Статус работы(В работе, Выполнено, Не выполнено)
 * @property string|null $activity_status_reason Причина невыполнения работы
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Schoolplan $schoolplan
 * @property User $author
 * @property User $executor
 */
class SchoolplanActivity extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan_activity';
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
                'class' => DateFieldBehavior::class,
                'attributes' => ['datetime_in'],
                'timeFormat' => 'd.m.Y H:i'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['schoolplan_id', 'author_id', 'executor_id', 'datetime_in', 'name'], 'required'],
            [['schoolplan_id', 'author_id', 'executor_id', 'activity_status', 'version'], 'integer'],
            [['datetime_in'], 'safe'],
            [['author_comment', 'executor_comment'], 'string'],
            [['name', 'places'], 'string', 'max' => 512],
            [['activity_status_reason'], 'string', 'max' => 1024],
            [['activity_status_reason'], 'required', 'when' => function ($model) {
                return $model->activity_status == 3;
            }, 'whenClient' => 'function(){return false;}', 'message' => 'Данное поле является обязательным.'],
            [['schoolplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schoolplan::class, 'targetAttribute' => ['schoolplan_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolplan_id' => 'Мероприятие',
            'author_id' => 'Автор работы',
            'executor_id' => 'Исполнитель работы',
            'datetime_in' => 'Дата и время работы',
            'name' => 'Название работы',
            'places' => 'Место работы',
            'author_comment' => 'Описание работы',
            'executor_comment' => 'Отчет исполнителя работы',
            'activity_status' => 'Статус работы',
            'activity_status_reason' => 'Причина невыполнения работы',
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    /**
     * Gets query for [[Schoolplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolplan()
    {
        return $this->hasOne(Schoolplan::class, ['id' => 'schoolplan_id']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    public function getExecutor()
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    public function getUsersListForExecutors()
    {
        $query = (new Query())->from('teachers_view')
            ->select('user_id as id , fullname as name')
            ->where(['teachers_id' => $this->schoolplan->executors_list])
            ->all();
        return \yii\helpers\ArrayHelper::map($query, 'id', 'name');
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

    public static function getAuthorId()
    {
        return Yii::$app->user->identity ? Yii::$app->user->identity->getId() : null;
    }

    public function isAuthor()
    {
        return $this->author_id == self::getAuthorId();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->activity_status != 3) {
            $this->activity_status_reason = null;
        }
        if (!$this->author_id && Art::isFrontend()) {
            $this->author_id = self::getAuthorId();
        }
        return parent::beforeSave($insert);
    }

    public function sendActivityMessage($content)
    {
        $title = 'Сообщение модуля "Планировщик мероприятия"';
        return Yii::$app->mailbox->mailing($this->executor_id, $content, $title);
    }
}
