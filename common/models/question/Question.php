<?php

namespace common\models\question;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "question".
 *
 * @property int $id
 * @property int $author_id
 * @property string $name Название формы
 * @property int $category_id Категория формы (Опрос, Заявка)
 * @property int $users_cat Группа пользователей (Сотрудники, Преподаватели, Ученики, Родители, Гости)
 * @property int $vid_id Вид формы (Открытая, Закрытая)
 * @property string $division_list Список отделений
 * @property string|null $description Описание формы
 * @property int $timestamp_in Начало действия формы
 * @property int $timestamp_out Окончание действия формы
 * @property int $status Статус формы (Активная, Не активная)
 * @property int $email_flag Отправлять пользователям информацию на E-mail при наличии формы?
 * @property int $email_author_flag Отправлять автору формы информацию на E-mail при каждом заполнении?
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Users $author
 * @property QuestionAttribute[] $questionAttributes
 * @property QuestionUsers[] $questionUsers
 */
class Question extends \artsoft\db\ActiveRecord
{
    const GROUP_GUEST = 0;
    const GROUP_EMPLOYEES = 1;
    const GROUP_TEACHERS = 2;
    const GROUP_STUDENTS = 3;
    const GROUP_PARENTS = 4;

    const CAT_SURVEY = 1;
    const CAT_APP = 2;

    const VID_OPEN = 1;
    const VID_CLOSE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question';
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
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'author_id',
            ],
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['division_list'],
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['timestamp_in', 'timestamp_out'],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'name', 'category_id', 'users_cat', 'vid_id', 'division_list', 'timestamp_in', 'timestamp_out'], 'required'],
            [['author_id', 'category_id', 'users_cat', 'vid_id', 'status'], 'default', 'value' => null],
            [['email_flag', 'email_author_flag'], 'default', 'value' => 0],
            [['author_id', 'category_id', 'users_cat', 'vid_id', 'status', 'email_flag', 'email_author_flag'], 'integer'],
            [['timestamp_in', 'timestamp_out'], 'safe'],
            [['division_list'], 'safe'],
            [['description'], 'string', 'max' => 1024],
            [['name'], 'string', 'max' => 127],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор Формы',
            'name' => 'Название формы',
            'category_id' => 'Категория формы',
            'users_cat' => 'Группа пользователей',
            'vid_id' => 'Вид формы',
            'division_list' => 'Список отделений',
            'description' => 'Описание формы',
            'timestamp_in' => 'Начало действия формы',
            'timestamp_out' => 'Окончание действия формы',
            'email_flag' => 'Отправлять пользователям информацию на E-mail при наличии формы?',
            'email_author_flag' => 'Отправлять автору формы информацию на E-mail при каждом заполнении?',
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    public static function getGroupList()
    {
        return array(
            self::GROUP_GUEST => 'Гости',
            self::GROUP_STUDENTS => 'Ученики',
            self::GROUP_EMPLOYEES => 'Сотрудники',
            self::GROUP_TEACHERS => 'Преподаватели',
            self::GROUP_PARENTS => 'Родители',
        );
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getGroupValue($val)
    {
        $ar = self::getGroupList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    public static function getCategoryList()
    {
        return array(
            self::CAT_SURVEY => 'Форма',
            self::CAT_APP => 'Заявка',
        );
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getCategoryValue($val)
    {
        $ar = self::getCategoryList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    public static function getVidList()
    {
        return array(
            self::VID_OPEN => 'Открытая',
            self::VID_CLOSE => 'Закрытая',
        );
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getVidValue($val)
    {
        $ar = self::getVidList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }
    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * Gets query for [[QuestionAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionAttributes()
    {
        return $this->hasMany(QuestionAttribute::className(), ['question_id' => 'id']);
    }

    /**
     * Gets query for [[QuestionUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionUsers()
    {
        return $this->hasMany(QuestionUsers::className(), ['question_id' => 'id']);
    }
}
