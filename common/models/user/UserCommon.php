<?php

namespace common\models\user;

use artsoft\behaviors\DateToTimeBehavior;
use artsoft\traits\DateTimeTrait;
use common\models\student\Student;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\db\ActiveRecord;

/**
 * This is the model class for table "user_common".
 *
 * @property int $id
 * @property int $user_category
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property int|null $birth_timestamp
 * @property int|null $gender
 * @property string|null $phone
 * @property string|null $phone_optional
 * @property string|null $snils
 * @property string|null $info
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $status
 * @property int $version
 *
 */
class UserCommon extends ActiveRecord
{
    use DateTimeTrait;

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;

    const GENDER_NOT_SET = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    
    const USER_CATEGORY_STAFF = 1;
    const USER_CATEGORY_TEACHER = 2;
    const USER_CATEGORY_STUDENT = 3;
    const USER_CATEGORY_PARENT = 4;
    
    public $birth_date;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user_common';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => DateToTimeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'birth_date',
                    ActiveRecord::EVENT_AFTER_FIND => 'birth_date',
                ],
                'timeAttribute' => 'birth_timestamp',
                'timeFormat' => 'd-m-Y',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'middle_name', 'last_name', 'birth_date'], 'required'],
            [['user_category', 'gender', 'status', 'version'], 'integer'],
            ['birth_timestamp', 'safe'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 124],
            [['first_name', 'middle_name', 'last_name'], 'trim'],
            [['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            ['last_name', 'unique', 'targetAttribute' => ['last_name', 'first_name', 'middle_name'], 'message' => Yii::t('art/auth', 'The user with the entered data already exists.')],
            [['phone', 'phone_optional'], 'string', 'max' => 24],
            [['snils'], 'string', 'max' => 16],
            ['info', 'string'],
            ['birth_date', 'date', 'format' => 'dd-MM-yyyy'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'user_category' => Yii::t('art', 'User Category'),
            'first_name' => Yii::t('art', 'First Name'),
            'middle_name' => Yii::t('art', 'Middle Name'),
            'last_name' => Yii::t('art', 'Last Name'),
            'fullName' => Yii::t('art', 'Full Name'),
            'birth_timestamp' => Yii::t('art', 'Birth Date'),
            'birth_date' => Yii::t('art', 'Birth Date'),
            'gender' => Yii::t('art', 'Gender'),
            'phone' => Yii::t('art', 'Phone'),
            'phone_optional' => Yii::t('art', 'Phone Optional'),
            'snils' => Yii::t('art', 'Snils'),
            'info' => Yii::t('art', 'Info'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }
    
     /* Геттер для полного имени человека */

    public function getFullName() {
        return $this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name;
    }
    /* Геттер для Фамилия И.О. */

    public function getLastFM() {
        return $this->last_name . ' ' . mb_substr((string) $this->first_name, 0, 1) . '.' . mb_substr((string) $this->middle_name, 0, 1) .'.';
    }
    
    public static function getUserCategoryValue($val)
    {
        $ar = self::getUserCategoryList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }
    /**
     * Get gender list
     * @return array
     */
    public static function getGenderList()
    {
        return [
            self::GENDER_NOT_SET => Yii::t('yii', '(not set)'),
            self::GENDER_MALE => Yii::t('art', 'Male'),
            self::GENDER_FEMALE => Yii::t('art', 'Female'),
        ];
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
        );
    }

    /**
     * Get gender list
     * @return array
     */
    public static function getUserCategoryList()
    {
        return [
            self::USER_CATEGORY_STAFF => Yii::t('art', 'Staff'),
            self::USER_CATEGORY_TEACHER => Yii::t('art', 'Teacher'),
            self::USER_CATEGORY_STUDENT => Yii::t('art', 'Student'),
            self::USER_CATEGORY_PARENT => Yii::t('art', 'Parent'),
        ];
    }
    /**
     * Первая буква заглавная
     */
    protected function getUcFirst($str, $encoding = 'UTF-8') {
        /* $str = mb_ereg_replace('^[\ ]+', '', $str);
          $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
          mb_substr($str, 1, mb_strlen($str), $encoding); */
        $str = mb_convert_case($str, MB_CASE_TITLE, $encoding);
        return $str;
    }
     /**
     * До валидации формируем строки с первой заглавной
     */
    public function beforeValidate() {

        $this->first_name = UserCommon::getUcFirst($this->first_name);
        $this->middle_name = UserCommon::getUcFirst($this->middle_name);
        $this->last_name = UserCommon::getUcFirst($this->last_name);


        return parent::beforeValidate();
    }

    /**
     * Функция возвращает массив id родителей, которых можно добавить к ученику.
     * Не учитываются уже добавленные родители
     * Вызывается в форме _form Student models
     */
     public static function getUserParentList($id)
    {
        $user_array[] = '0'; // для работы 'not in' с пустым массивом
        foreach (Student::getFamilyList($id) as $item)  $user_array[] = $item['user_id'];

        return \yii\helpers\ArrayHelper::map(UserCommon::find()
            ->andWhere(['not in', 'user.status', User::STATUS_BANNED]) // заблокированных не добавляем в список
            ->andWhere(['in', 'user.user_category', User::USER_CATEGORY_PARENT]) // только родителей
            ->andWhere(['not in', 'user.id', $user_array]) // не добавляем уже добавленных родителей
            ->select(['user.id as user_id', "CONCAT(user.last_name, ' ',user.first_name, ' ',user.middle_name) AS name"])
            ->orderBy('user.last_name')
            ->asArray()->all(), 'user_id', 'name');
    }
    
    public static function getWorkAuthorTeachersList()
    {
        return \yii\helpers\ArrayHelper::map(UserCommon::find()
            ->andWhere(['not in', 'user.status', User::STATUS_BANNED]) // заблокированных не добавляем в список
            ->andWhere(['in', 'user.user_category', User::USER_CATEGORY_TEACHER]) // только преподаватели
            ->select(['user.id as user_id', "CONCAT(user.last_name, ' ',user.first_name, ' ',user.middle_name) AS name"])
            ->orderBy('user.last_name')
            ->asArray()->all(), 'user_id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(self::class, ['id' => 'created_by']);
    } /**
 * @return \yii\db\ActiveQuery
 */
    public function getUpdatedBy()
    {
        return $this->hasOne(self::class, ['id' => 'updated_by']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(self::class, ['user_id' => 'id']);
    }
}
