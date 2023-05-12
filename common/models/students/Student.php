<?php

namespace common\models\students;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\db\ActiveRecord;
use common\models\user\UserCommon;
use common\models\students\StudentPosition;
use Yii;
use artsoft\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "students".
 *
 * @property int $id
 * @property int $user_common_id
 * @property string $sert_name
 * @property string $sert_series
 * @property string $sert_num
 * @property string $sert_organ
 * @property string $sert_date
 *
 * @property UserCommon $user
 */
class Student extends ActiveRecord
{
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
            [['user_common_id'], 'required'],
            [['sert_date'], 'safe'],
            [['sert_name', 'sert_series', 'sert_num'], 'string', 'max' => 32],
            [['sert_organ'], 'string', 'max' => 127],
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
            [['user_common_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['user_common_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/student', 'ID'),
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
    public function getUser()
    {
        return $this->hasOne(UserCommon::class, ['id' => 'user_common_id']);
    }

    public function getUserStatus()
    {
        return $this->user ? $this->user->status : null;
    }

    public function getUserBirthDate()
    {
        return $this->user ? $this->user->birth_date : null;
    }

    public function getUserPhone()
    {
        return $this->user ? ($this->user->phone ? $this->user->phone : $this->user->phone_optional) : null;
    }

    public function getUserAddress()
    {
        return $this->user ? $this->user->address : null;
    }

    public function getUserEmail()
    {
        return $this->user ? $this->user->email : null;
    }

    public static function getDocumentValue($val)
    {
        $ar = self::STUDENT_DOC;

        return isset($ar[$val]) ? $ar[$val] : $val;
    }
    /**
     * Геттер полного имени юзера
     */
    public function getFullName()
    {
        return $this->user ? $this->user->fullName : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getStudentDependence()
    {
        return $this->hasMany(StudentDependence::class, ['student_id' => 'id']);
    }

    public function getStudentDependenceNameById($student_id)
    {
        return StudentDependence::find(['student_id' => $student_id])
            ->innerJoin('userRelation');
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        $model = UserCommon::findOne($this->user_common_id);
        if (!$model->delete(false)) {
            return false;
        }
        foreach (StudentDependence::findAll(['student_id' => $this->id]) as $model) {
            if (!$model->delete(false)) {
                break;
                return false;
            }
        }

        return parent::beforeDelete();
    }

}
