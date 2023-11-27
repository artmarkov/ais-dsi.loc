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

    const DOC_STATUS_DRAFT = 0; //Черновик (внесены изменения) - серый
    const DOC_STATUS_AGREED = 1; //Согласовано - зеленый
    const DOC_STATUS_WAIT = 2; //На согласовании - желтый
   // const DOC_STATUS_CANCEL = 3; //Отменено - красный

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
     * getDocStatusList
     * @return array
     */
    public static function getDocStatusList()
    {
        return array(
            self::DOC_STATUS_DRAFT => Yii::t('art', 'Draft'),
            self::DOC_STATUS_AGREED => Yii::t('art', 'Agreed'),
            self::DOC_STATUS_WAIT => Yii::t('art', 'Wait'),
          //  self::DOC_STATUS_CANCEL => Yii::t('art', 'Canceled'),
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

    /**
     * getDocStatusValue
     * @param $val
     * @return mixed
     */
    public static function getDocStatusValue($val)
    {
        $ar = self::getDocStatusList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }
}
