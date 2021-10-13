<?php

namespace common\models\subject;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_subject_type".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $type_id
 * @property int $status
 */
class SubjectType extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const BASIS_FREE = 0;
    const BASIS_PAY = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_subject_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'type_id', 'status'], 'required'],
            [['status', 'type_id'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['slug'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name'),
            'slug' => Yii::t('art/guide', 'Slug'),
            'type_id' => Yii::t('art/guide', 'Subject Type Name'),
            'status' => Yii::t('art/guide', 'Status'),
        ];
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getBasisList()
    {
        return array(
            self::BASIS_FREE => Yii::t('art/guide', 'Basis Free'),
            self::BASIS_PAY => Yii::t('art/guide', 'Basis Pay'),
        );
    }
    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList() {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
        );
    }
    /**
     * getStatusValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getStatusValue($val) {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return ArrayHelper::map(self::find()
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->select('id, name')
            ->orderBy('id')
            ->asArray()->all(), 'id', 'name');
    }

}
