<?php

namespace common\models\student;

use common\models\user\UserFamily;
use Yii;
use artsoft\models\User;

/**
 * This is the model class for table "{{%student}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $position_id
 * @property string $sertificate_name
 * @property string $sertificate_series
 * @property string $sertificate_num
 * @property string $sertificate_organ
 * @property string $sertificate_timestamp
 *
 * @property StudentPosition $position
 * @property User $user
 */
class Student extends \yii\db\ActiveRecord
{
    public $user_slave_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%student}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position_id'], 'required'],
            [['user_id', 'position_id'], 'integer'],
            [['sertificate_timestamp'], 'safe'],
            [['sertificate_name', 'sertificate_series', 'sertificate_num'], 'string', 'max' => 32],
            [['sertificate_organ'], 'string', 'max' => 127],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentPosition::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            // при заполнении одного из полей, делаем обязательными остальные поля блока документа
            [['sertificate_series', 'sertificate_num', 'sertificate_organ', 'sertificate_timestamp'], 'required', 'when' => function ($model) { return $model->sertificate_name != NULL; }, 'enableClientValidation' => false],
            [['sertificate_name', 'sertificate_num', 'sertificate_organ', 'sertificate_timestamp'], 'required', 'when' => function ($model) { return $model->sertificate_series != NULL; }, 'enableClientValidation' => false],
            [['sertificate_name', 'sertificate_series', 'sertificate_organ', 'sertificate_timestamp'], 'required', 'when' => function ($model) { return $model->sertificate_num != NULL; }, 'enableClientValidation' => false],
            [['sertificate_name', 'sertificate_num', 'sertificate_series', 'sertificate_timestamp'], 'required', 'when' => function ($model) { return $model->sertificate_organ != NULL; }, 'enableClientValidation' => false],
            [['sertificate_name', 'sertificate_num', 'sertificate_series', 'sertificate_organ'], 'required', 'when' => function ($model) { return $model->sertificate_timestamp != NULL; }, 'enableClientValidation' => false],
            ['sertificate_timestamp', 'date', 'timestampAttribute' => 'sertificate_timestamp', 'format' => 'dd-MM-yyyy'],
            ['sertificate_timestamp', 'default', 'value' =>  NULL],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/student', 'ID'),
            'user_id' => Yii::t('art/student', 'User ID'),
            'position_id' => Yii::t('art/student', 'Position ID'),
            'sertificate_name' => Yii::t('art/student', 'Sertificate Name'),
            'sertificate_series' => Yii::t('art/student', 'Sertificate Series'),
            'sertificate_num' => Yii::t('art/student', 'Sertificate Num'),
            'sertificate_organ' => Yii::t('art/student', 'Sertificate Organ'),
            'sertificate_timestamp' => Yii::t('art/student', 'Sertificate Date'),
            'studentsFullName' => Yii::t('art', 'Full Name'),
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(StudentPosition::className(), ['id' => 'position_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    /**
     * Геттер полного имени юзера
     */
    public function getStudentsFullName()
    {
        return $this->user->fullName;
    }
    /**
     * Геттер даты рождения
     */
    public function getBirthDate()
    {
        return Yii::$app->formatter->asDate(($this->isNewRecord) ? time() : $this->user->birth_timestamp);
    }
    /**
     * @return \yii\db\ActiveQuery
     */

    public function getUserFamily()
    {
        return $this->hasMany(UserFamily::className(), ['user_main_id' => 'user_id']);
    }
    /**
     * Список родителей ученика
     * @param type $user_id
     * @return array
     */
    
    public static function getFamilyList($user_id)
    {
        $data = UserFamily::find()
            ->innerJoin('user_relation', 'user_relation.id = user_family.relation_id')
            ->innerJoin('user', 'user.id = user_family.user_slave_id')
            ->andWhere(['in', 'user_family.user_main_id' , $user_id])
            ->select(['user.id as user_id',
                      'user_family.id as id',
                      "CONCAT(user.last_name, ' ',user.first_name, ' ',user.middle_name) AS parent",
                      'user_relation.name as relation',
                      'user.phone as phone',
                      'user.email as email'
                ])
            ->orderBy('user.last_name')
            ->asArray()->all(); 

      return $data; 
    }
}
