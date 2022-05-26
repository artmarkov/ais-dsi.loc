<?php

namespace common\models\employees;

use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "employees".
 *
 * @property int $id
 * @property int|null $user_common_id
 * @property string|null $position
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 * @property int $access_work_flag Разрешение на доступ к работе получено
 */
class Employees extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees';
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id', 'version', 'access_work_flag'], 'integer'],
            [['access_work_flag'], 'default', 'value' => 0],
            [['position'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/employees', 'ID'),
            'position' => Yii::t('art/employees', 'Position'),
            'fullName' => Yii::t('art', 'Full Name'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'userStatus' => Yii::t('art', 'Status'),
            'access_work_flag' => 'Разрешение на доступ к работе получено'
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
    /**
     * Геттер полного имени юзера
     */
    public function getFullName()
    {
        return $this->user ? $this->user->fullName : null;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        $model = UserCommon::findOne($this->user_common_id);
        if(!$model->delete()){
            return false;
        }
        return parent::beforeDelete();
    }
}
