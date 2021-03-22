<?php

namespace common\models\auditory;

use Yii;

/**
 * This is the model class for table "auditory_building".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $address
 */
class AuditoryBuilding extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditory_building';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'address'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['slug'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name Building'),
            'slug' => Yii::t('art/guide', 'Slug'),
            'address' => Yii::t('art/guide', 'Address'),
        ];
    }
    public function getAuditory()
    {
        return $this->hasMany(Auditory::className(), ['building_id' => 'id']);
        
    }

    public static function getAuditoryBuildingList()
    {
        return \yii\helpers\ArrayHelper::map(AuditoryBuilding::find()->all(), 'id', 'name');
    }
}
