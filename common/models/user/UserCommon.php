<?php

namespace common\models\user;

use common\models\student\Student;
use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $status
 * @property int $user_category
 * @property int $created_at
 * @property int $updated_at
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property int $birth_timestamp
 * @property int $gender
 * @property string $phone
 * @property string $phone_optional 
 * @property string $snils
 * 
 */
class UserCommon extends \artsoft\models\UserIdentity
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return Yii::$app->art->user_table;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'middle_name', 'last_name', 'birth_timestamp'], 'required'],
            [['status', 'user_category', 'gender'], 'integer'],
            ['birth_timestamp', 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 124],
            [['first_name', 'middle_name', 'last_name'], 'trim'],
            [['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => Yii::$app->art->cyrillicRegexp, 'message' => Yii::t('art', 'Only need to enter Russian letters')],
            ['last_name', 'unique', 'targetAttribute' => ['last_name', 'first_name', 'middle_name'], 'message' => Yii::t('art/auth', 'The user with the entered data already exists.')],
            [['phone', 'phone_optional'], 'string', 'max' => 24],           
            [['snils'], 'string', 'max' => 16],
            ['birth_timestamp', 'date', 'timestampAttribute' => 'birth_timestamp', 'format' => 'dd-MM-yyyy'],
            ['birth_timestamp', 'default', 'value' =>  mktime(0,0,0, date("m", time()), date("d", time()), date("Y", time()))],
            
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'status' => Yii::t('art', 'Status'),
            'user_category' => Yii::t('art', 'User Category'),
            'first_name' => Yii::t('art', 'First Name'),
            'middle_name' => Yii::t('art', 'Middle Name'),
            'last_name' => Yii::t('art', 'Last Name'),
            'birth_timestamp' => Yii::t('art', 'Birth Date'),
            'gender' => Yii::t('art', 'Gender'),
            'phone' => Yii::t('art', 'Phone'),
            'phone_optional' => Yii::t('art', 'Phone Optional'),
            'snils' => Yii::t('art', 'Snils'),
            'created_at' => Yii::t('art', 'Created At'),
            'updated_at' => Yii::t('art', 'Updated At'),
            'fullName' => Yii::t('art', 'Full Name'),
        ];
    }
     /* Геттер для полного имени человека */

    public function getFullName() {
        return $this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name;
    }
    /* Геттер для Фамилия И.О. */

    public function getLastFM() {
        return $this->last_name . ' ' . mb_substr((string) $this->first_name, 0, 1) . '.' . mb_substr((string) $this->middle_name, 0, 1) .'.';
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
   
}
