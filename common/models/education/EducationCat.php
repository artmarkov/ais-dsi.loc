<?php

namespace common\models\education;

use Yii;

/**
 * This is the model class for table "guide_education_cat".
 *
 * @property int $id
 * @property string|null $name
 * @property string $short_name
 * @property int $status
 *
 * @property EducationProgramm[] $educationProgramms
 */
class EducationCat extends \artsoft\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_education_cat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name', 'status'], 'required'],
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['short_name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'name' => Yii::t('art', 'Name'),
            'short_name' => Yii::t('art', 'Short Name'),
            'status' => Yii::t('art', 'Status'),
        ];
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
     * Gets query for [[EducationProgramms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationProgramms()
    {
        return $this->hasMany(EducationProgramm::className(), ['education_cat_id' => 'id']);
    }
}
