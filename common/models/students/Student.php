<?php

namespace common\models\students;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\traits\DateTimeTrait;
use common\models\user\UserCommon;
use common\models\user\UserFamily;
use Yii;
use artsoft\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "students".
 *
 * @property int $id
 * @property int $user_id
 * @property int $position_id
 * @property string $sert_name
 * @property string $sert_series
 * @property string $sert_num
 * @property string $sert_organ
 * @property string $sert_date
 *
 * @property StudentPosition $position
 * @property User $user
 */
class Student extends \yii\db\ActiveRecord
{
    use DateTimeTrait;

    const STUDENT_DOC = [
        'password' => 'Паспорт',
        'birth_cert' => 'Свидетельство о рождении',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'students';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
//            [
//                'class' => ArrayFieldBehavior::class,
//                'attributes' => ['bonus_list', 'department_list'],
//            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['sert_date'],
            ],
            [
                'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position_id'], 'required'],
            [['position_id'], 'integer'],
            [['sert_date'], 'safe'],
            [['sert_name', 'sert_series', 'sert_num'], 'string', 'max' => 32],
            [['sert_organ'], 'string', 'max' => 127],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentPosition::className(), 'targetAttribute' => ['position_id' => 'id']],
            // при заполнении одного из полей, делаем обязательными остальные поля блока документа
            [['sert_series', 'sert_num', 'sert_organ', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_name != NULL;
            }, 'enableClientValidation' => false],
            [['sert_name', 'sert_num', 'sert_organ', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_series != NULL;
            }, 'enableClientValidation' => false],
            [['sert_name', 'sert_series', 'sert_organ', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_num != NULL;
            }, 'enableClientValidation' => false],
            [['sert_name', 'sert_num', 'sert_series', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_organ != NULL;
            }, 'enableClientValidation' => false],
            [['sert_name', 'sert_num', 'sert_series', 'sert_organ'], 'required', 'when' => function ($model) {
                return $model->sert_date != NULL;
            }, 'enableClientValidation' => false],
            ['sert_date', 'default', 'value' => NULL],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/student', 'ID'),
            'position_id' => Yii::t('art/student', 'Position'),
            'sert_name' => Yii::t('art/student', 'Sertificate Name'),
            'sert_series' => Yii::t('art/student', 'Sertificate Series'),
            'sert_num' => Yii::t('art/student', 'Sertificate Num'),
            'sert_organ' => Yii::t('art/student', 'Sertificate Organ'),
            'sert_date' => Yii::t('art/student', 'Sertificate Date'),
            'fullName' => Yii::t('art', 'Full Name'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'userStatus' => Yii::t('art', 'Status'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
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
        return $this->hasOne(UserCommon::class, ['id' => 'user_common_id']);
    }

    public function getUserStatus()
    {
        return $this->user->status;
    }

    public function getUserBirthDate()
    {
        return $this->user->birth_date;
    }

    /**
     * Геттер полного имени юзера
     */
    public function getFullName()
    {
        return $this->user->fullName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

//    public function getUserFamily()
//    {
//        return $this->hasMany(UserFamily::className(), ['user_main_id' => 'user_id']);
//    }
    /**
     * Список родителей ученика
     * @param type $user_id
     * @return array
     */

//    public static function getFamilyList($user_id)
//    {
//        $data = UserFamily::find()
//            ->innerJoin('user_relation', 'user_relation.id = user_family.relation_id')
//            ->innerJoin('user', 'user.id = user_family.user_slave_id')
//            ->andWhere(['in', 'user_family.user_main_id' , $user_id])
//            ->select(['user.id as user_id',
//                      'user_family.id as id',
//                      "CONCAT(user.last_name, ' ',user.first_name, ' ',user.middle_name) AS parent",
//                      'user_relation.name as relation',
//                      'user.phone as phone',
//                      'user.email as email'
//                ])
//            ->orderBy('user.last_name')
//            ->asArray()->all();
//
//      return $data;
//    }
}
