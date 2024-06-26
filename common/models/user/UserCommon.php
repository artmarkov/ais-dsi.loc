<?php

namespace common\models\user;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\Schedule;
use artsoft\models\User;
use common\models\service\UsersCard;
use common\models\students\Student;
use dosamigos\transliterator\TransliteratorHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\db\ActiveRecord;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 **
 * This is the model class for table "user_common".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $user_category
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $address
 * @property int|null $birth_date
 * @property int|null $gender
 * @property string|null $phone
 * @property string|null $phone_optional
 * @property string|null $snils
 * @property string|null $info
 * @property string|null $email
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 */

class UserCommon extends ActiveRecord
{
    const GENDER_NOT_SET = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    const USER_CATEGORY_EMPLOYEES = 'employees';
    const USER_CATEGORY_TEACHERS = 'teachers';
    const USER_CATEGORY_STUDENTS = 'students';
    const USER_CATEGORY_PARENTS = 'parents';

    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_REG = 'registration';
    const SCENARIO_STUDENT = 'student';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_common';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['birth_date'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['birth_date'], 'required', 'on'=> self::SCENARIO_STUDENT, 'enableClientValidation' => false],
            [['gender', 'status', 'version'], 'integer'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['user_category', 'first_name', 'middle_name', 'last_name', 'address', 'email'], 'string', 'max' => 124],
            [['first_name', 'middle_name', 'last_name'], 'trim'],
            [['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
//            [['last_name'], 'unique', 'targetAttribute' => ['last_name', 'first_name', 'middle_name'],
//                'message' => Yii::t('art/auth', 'The user with the entered data already exists.'), 'except' => self::SCENARIO_REG],
            [['phone', 'phone_optional'], 'string', 'max' => 24],
            [['snils'], 'string', 'max' => 16],
            ['info', 'string'],
            ['email', 'email'],
            ['birth_date', 'safe'],
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
            'address' => Yii::t('art/guide', 'Address'),
            'birth_date' => Yii::t('art', 'Birth Date'),
            'gender' => Yii::t('art', 'Gender'),
            'phone' => Yii::t('art', 'Phone'),
            'phone_optional' => Yii::t('art', 'Phone Optional'),
            'snils' => Yii::t('art', 'Snils'),
            'info' => Yii::t('art', 'Info'),
            'email' => Yii::t('art', 'Email for Mailing'),
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

    public function getFullName()
    {
        return $this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name;
    }

    /* Геттер для Фамилия И.О. */

    public function getLastFM()
    {
        return $this->last_name . ' ' . mb_substr((string)$this->first_name, 0, 1) . '.' . mb_substr((string)$this->middle_name, 0, 1) . '.';
    }

    public function generateUsername($userName = null)
    {
        $last_name = $this->slug($this->last_name);
        $first_name = $this->slug($this->first_name);
        $middle_name = $this->slug($this->middle_name);

        $i = 0;

        do {
            $username = $last_name . '-' . substr($first_name, 0, ++$i) . substr($middle_name, 0, 1);
        } while (User::findByUsername($username) or $username == $userName);

        return $username;
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
     * getGenderValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getGenderValue($val)
    {
        $ar = self::getGenderList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * Get gender list
     * @return array
     */
    public static function getUserCategoryList()
    {
        return [
            self::USER_CATEGORY_EMPLOYEES => Yii::t('art', 'Staff'),
            self::USER_CATEGORY_TEACHERS => Yii::t('art', 'Teacher'),
            self::USER_CATEGORY_STUDENTS => Yii::t('art', 'Student'),
            self::USER_CATEGORY_PARENTS => Yii::t('art', 'Parent'),
        ];
    }

    public static function getUserCategoryValue($val)
    {
        $ar = self::getUserCategoryList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }


    /**
     * @return mixed
     */
    public function getRelatedTable()
    {
        return $this->user_category;

    }

    /**
     * @return false|int|null|string
     */
    public function getRelatedId()
    {
        return self::find()->select(self::getRelatedTable() . '.id')->innerJoin(self::getRelatedTable(), 'user_common_id = user_common.id')
            ->where(['=', 'user_common.id', $this->id])
            ->scalar();
    }

    /**
     * @return string
     */
    public function getRelatedUrl()
    {
        return Url::to(['/' . self::getRelatedTable() . '/default/view', 'id' => self::getRelatedId()]);

    }

    /**
     * Первая буква заглавная
     */
    protected function getUcFirst($str, $encoding = 'UTF-8')
    {
        return mb_convert_case($str, MB_CASE_TITLE, $encoding);
    }

    /**
     * До валидации формируем строки с первой заглавной
     */
    public function beforeValidate()
    {
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
        foreach (Student::getFamilyList($id) as $item) $user_array[] = $item['user_id'];

        return \yii\helpers\ArrayHelper::map(self::find()
            //->andWhere(['not in', 'user.status', User::STATUS_BANNED])// заблокированных не добавляем в список
            ->andWhere(['in', 'user.user_category', self::USER_CATEGORY_PARENTS])// только родителей
            ->andWhere(['not in', 'user.id', $user_array])// не добавляем уже добавленных родителей
            ->select(['user.id as user_id', "CONCAT(user.last_name, ' ',user.first_name, ' ',user.middle_name) AS name"])
            ->orderBy('user.last_name')
            ->asArray()->all(), 'user_id', 'name');
    }

    public static function getTeachersList()
    {
        return \yii\helpers\ArrayHelper::map(self::find()
            ->andWhere(['in', 'user_common.status', self::STATUS_ACTIVE])// заблокированных не добавляем в список
            ->andWhere(['in', 'user_common.user_category', self::USER_CATEGORY_TEACHERS])// только преподаватели
            ->select(['user_common.id as user_id', "CONCAT(user_common.last_name, ' ',user_common.first_name, ' ',user_common.middle_name) AS name"])
            ->orderBy('user_common.last_name')
            ->asArray()->all(), 'user_id', 'name');
    }

    /**
     * getUsersCommonListByCategory
     *
     * @return array
     */
    public static function getUsersCommonListByCategory($category = [])
    {
        $users = static::find()->select([
            'id',
            'CONCAT(last_name, \' \',first_name, \' \',middle_name) as fullname',
            'CASE
                  WHEN (user_category = \'employees\') THEN \'' . static::getUserCategoryValue('employees') . '\'
                  WHEN (user_category = \'teachers\') THEN \'' . static::getUserCategoryValue('teachers') . '\'
                  WHEN (user_category = \'students\') THEN \'' . static::getUserCategoryValue('students') . '\'
                  WHEN (user_category = \'parents\') THEN \'' . static::getUserCategoryValue('parents') . '\'
                  ELSE \'\'
            END as category_name'
        ])
            ->where(['in', 'user_category', $category])
            ->andWhere(['=', 'status', self::STATUS_ACTIVE])
            ->orderBy('category_name, fullname')
            ->asArray()->all();
        return ArrayHelper::map($users, 'id', 'fullname', 'category_name');
    }

    /**
     * Дни рождения на дату для выбранных категорий
     * @param array $category
     * @param bool $timestamp
     * @return array|UserCommon[]|\yii\db\ActiveRecord[]
     */
    public static function getUsersBirthdayByCategory($category = [], $timestamp = false)
    {
        $timestamp = $timestamp ?: time();
        return static::find()
            ->select([
                'id',
                'CONCAT(last_name, \' \',first_name, \' \',middle_name) as fullname',
                'CASE
                  WHEN (user_category = \'employees\') THEN \'Сотрудник\'
                  WHEN (user_category = \'teachers\') THEN \'Преподаватель\'
                  WHEN (user_category = \'students\') THEN \'Ученик\'
                  WHEN (user_category = \'parents\') THEN \'Родитель\'
                  ELSE \'\'
            END as category_name',
                'birth_date'
            ])
            ->where(['user_category' => $category])
            ->andWhere(['=', 'status', self::STATUS_ACTIVE])
            ->andWhere(new \yii\db\Expression("date_part('day', to_timestamp(birth_date + 14400)) = date_part('day', to_timestamp(:timestamp))"), [':timestamp' => $timestamp])
            ->andWhere(new \yii\db\Expression("date_part('month', to_timestamp(birth_date + 14400)) = date_part('month', to_timestamp(:timestamp))"), [':timestamp' => $timestamp])
            ->orderBy('category_name')
            ->asArray()
            ->all();
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Возвращает версии объекта
     * @return User[]
     */
    public function getVersions()
    {
        $rows = (new \yii\db\Query)
            ->from('users_common_hist')
            ->where(['id' => $this->id])
            ->orderBy('hist_id')
            ->all();
        return array_map(function ($item) {
            unset($item['hist_id']);
            unset($item['op']);
            return new User($item);
        }, $rows);
    }

    /**
     * Slug translit
     *
     * @param string $slug
     * @return static|null
     */
    protected static function slug($string, $replacement = '-', $lowercase = true)
    {
        $string = preg_replace('/[^\p{L}\p{Nd}]+/u', $replacement, $string);
        $string = TransliteratorHelper::process($string, 'UTF-8');
        return $lowercase ? mb_strtolower($string) : $string;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        $model = User::findOne($this->user_id);
        if ($model) {
            if (!$model->delete()) {
                return false;
            }
        }
        $model = UsersCard::findOne(['user_common_id' => $this->id]);
        if ($model) {
            if (!$model->delete()) {
                return false;
            }
        }
        return parent::beforeDelete();
    }
}
