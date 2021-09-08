<?php

namespace common\models\parents;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use common\models\students\StudentDependence;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "parents".
 *
 * @property int $id
 * @property int|null $user_common_id
 * @property string|null $sert_name
 * @property string|null $sert_series
 * @property string|null $sert_num
 * @property string|null $sert_organ
 * @property int|null $sert_date
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 */
class Parents extends \artsoft\db\ActiveRecord
{
    use DateTimeTrait;

    const PARENT_DOC = [
        'password' => 'Паспорт',
        'military_card' => 'Военный билет',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'parents';
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
            [['user_common_id', 'version'], 'integer'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'sert_date'], 'safe'],
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/parents', 'ID'),
            'sert_name' => Yii::t('art/parents', 'Sertificate Name'),
            'sert_series' => Yii::t('art/parents', 'Sertificate Series'),
            'sert_num' => Yii::t('art/parents', 'Sertificate Num'),
            'sert_organ' => Yii::t('art/parents', 'Sertificate Organ'),
            'sert_date' => Yii::t('art/parents', 'Sertificate Date'),
            'fullName' => Yii::t('art', 'Full Name'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
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

    public static function getDocumentValue($val)
    {
        $ar = self::PARENT_DOC;

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
    public function getStudentDependence()
    {
        return $this->hasMany(StudentDependence::className(), ['parent_id' => 'id']);
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
        foreach (StudentDependence::findAll(['parent_id' => $this->id]) as $model) {
            if (!$model->delete(false)) {
                break;
                return false;
            }
        }
        return parent::beforeDelete();
    }
}
