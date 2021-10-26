<?php

namespace artsoft\db;

use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use Yii;

/**
 * @inheritdoc
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    use DateTimeTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Returns TRUE if model support multilingual behavior.
     *
     * @param ActiveRecord $model
     * @return boolean
     */
    public function isMultilingual()
    {
        return ($this->getBehavior('multilingual') !== NULL);
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
     * getStatusValue
     * @param string $val
     * @return string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }
}
