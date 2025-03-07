<?php

namespace common\models\parents;

use artsoft\behaviors\DateFieldBehavior;
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
 * @property string|null $sert_code
 * @property string|null $sert_country
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property UserCommon $user
 * @property StudentDependence $studentDependence
 */
class Parents extends \artsoft\db\ActiveRecord
{
    const PARENT_DOC = [
        'password' => 'Паспорт',
        'military_card' => 'Военный билет',
        'password_foreign' => 'Паспорт иностранного гражданина',
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
            [['sert_name', 'sert_series', 'sert_num', 'sert_code'], 'string', 'max' => 32],
            [['sert_organ', 'sert_country'], 'string', 'max' => 127],
            // при заполнении одного из полей, делаем обязательными остальные поля блока документа
            [['sert_num', 'sert_organ', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_series != NULL && $model->sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#parents-sert_series').val() != NULL && $('#parents-sert_name').val() != 'pasport_foreign';
                    }"],
            [['sert_series', 'sert_organ', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_num != NULL && $model->sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#parents-sert_num').val() != NULL && $('#parents-sert_name').val() != 'pasport_foreign';
                    }"],
            [['sert_series', 'sert_num', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_organ != NULL && $model->sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#parents-sert_organ').val() != NULL && $('#parents-sert_name').val() != 'pasport_foreign';
                    }"],
            [['sert_series', 'sert_num', 'sert_organ', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_code != NULL && $model->sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#parents-sert_code').val() != NULL && $('#parents-sert_name').val() != 'pasport_foreign';
                    }"],
            [['sert_series', 'sert_num', 'sert_organ'], 'required', 'when' => function ($model) {
                return $model->sert_date != NULL && $model->sert_name != 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#parents-sert_date').val() != NULL && $('#parents-sert_name').val() != 'pasport_foreign';
                    }"],
            [['sert_num', 'sert_country'], 'required', 'when' => function ($model) {
                return $model->sert_name == 'password_foreign';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#parents-sert_name').val() == 'pasport_foreign';
                    }"],
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
            'sert_code' => Yii::t('art/parents', 'Sertificate Code'),
            'sert_country' => Yii::t('art/parents', 'Sertificate Country'),
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

    public function getUserEmail()
    {
        return $this->user ? $this->user->email : null;
    }

    public function getUserSnils()
    {
        return $this->user ? $this->user->snils : null;
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
        foreach ($this->studentDependence as $model) {
            if (!$model->delete(false)) {
                break;
                return false;
            }

            $model = $this->user;
            if (!$model->delete(false)) {
                return false;
            }
        }
        return parent::beforeDelete();
    }
}
