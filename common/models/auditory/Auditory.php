<?php

namespace common\models\auditory;

use Yii;

/**
 * This is the model class for table "{{%auditory}}".
 *
 * @property int $id
 * @property int $building_id
 * @property int $cat_id
 * @property string $study_flag
 * @property int $num
 * @property string $name
 * @property string $slug
 * @property string $floor
 * @property double $area
 * @property int $capacity
 * @property string $description
 * @property int $order
 */
class Auditory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%auditory}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','building_id', 'study_flag', 'floor', 'area', 'capacity', 'order'], 'required'],
            [['id','building_id', 'cat_id', 'num', 'capacity', 'order'], 'integer'],
            [['study_flag'], 'string'],
            [['area'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['floor'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'building_id' => Yii::t('art/guide', 'Building ID'),
            'cat_id' => Yii::t('art/guide', 'Cat ID'),
            'study_flag' => Yii::t('art/guide', 'Study Flag'),
            'num' => Yii::t('art/guide', 'Num Auditory'),
            'name' => Yii::t('art/guide', 'Name Auditory'),
            'floor' => Yii::t('art/guide', 'Floor'),
            'area' => Yii::t('art/guide', 'Area Auditory'),
            'capacity' => Yii::t('art/guide', 'Capacity Auditory'),
            'description' => Yii::t('art/guide', 'Description Auditory'),
            'order' => Yii::t('art/guide', 'Order'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(AuditoryCat::className(), ['id' => 'cat_id']);
    }

    /* Геттер для названия категории */
    public function getCatName()
    {
        return $this->cat->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(AuditoryBuilding::className(), ['id' => 'building_id']);
    }

    /* Геттер для названия здания */
    public function getBuildingName()
    {
        return $this->building->name;
    }
}
