<?php

namespace common\models\parents;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
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
//            [
//                'class' => ArrayFieldBehavior::class,
//                'attributes' => ['bonus_list', 'department_list'],
//            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['sert_date'],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id', 'sert_date', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'default', 'value' => null],
            [['user_common_id', 'version'], 'integer'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'sert_date'], 'safe'],
            [['sert_name', 'sert_series', 'sert_num'], 'string', 'max' => 32],
            [['sert_organ'], 'string', 'max' => 127],
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
            'parentsFullName' => Yii::t('art', 'Full Name'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
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
        return $this->user->status;
    }

    public function getUserBirthDate()
    {
        return $this->user->birth_date;
    }

    /**
     * Геттер полного имени юзера
     */
    public function getParentsFullName()
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

}
