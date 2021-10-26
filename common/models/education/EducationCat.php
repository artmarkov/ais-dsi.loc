<?php

namespace common\models\education;

use common\models\subject\SubjectType;
use Yii;

/**
 * This is the model class for table "guide_education_cat".
 *
 * @property int $id
 * @property string|null $name
 * @property string $short_name
 * @property int $type_id
 * @property int $status
 *
 * @property EducationProgramm[] $educationProgramms
 */
class EducationCat extends \artsoft\db\ActiveRecord
{

    const BASIS_FREE = 0;
    const BASIS_PAY = 1;

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
            [['name', 'short_name', 'type_id', 'status'], 'required'],
            [['status'], 'default', 'value' => null],
            [['status', 'type_id'], 'integer'],
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
            'type_id' => Yii::t('art/guide', 'Subject Type Name'),
            'status' => Yii::t('art', 'Status'),
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
     * Gets query for [[EducationProgramms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationProgramms()
    {
        return $this->hasMany(EducationProgramm::class, ['education_cat_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function getEducationCatList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }
    
}
