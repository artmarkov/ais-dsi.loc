<?php

namespace common\models\venue;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_venue_district".
 *
 * @property int $id
 * @property int $sity_id
 * @property string $name
 * @property string $slug
 *
 * @property VenueSity $sity
 * @property VenuePlace[] $venuePlaces
 */
class VenueDistrict extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_venue_district';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sity_id', 'name', 'slug'], 'required'],
            [['sity_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 16],
            [['sity_id'], 'exist', 'skipOnError' => true, 'targetClass' => VenueSity::className(), 'targetAttribute' => ['sity_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'sity_id' => Yii::t('art/guide', 'Sity ID'),
            'name' => Yii::t('art/guide', 'Name District'),
            'slug' => Yii::t('art/guide', 'Slug'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSity()
    {
        return $this->hasOne(VenueSity::className(), ['id' => 'sity_id']);
    }

    /* Геттер для названия города */
    public function getSityName()
    {
        return $this->sity->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenuePlaces()
    {
        return $this->hasMany(VenuePlace::className(), ['district_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     * Полный список районов
     */
    public static function getVenueDistrictList() {
        return ArrayHelper::map(VenueDistrict::find()->all(), 'id', 'name');
    }
    /**
     * @return \yii\db\ActiveQuery
     * Полный список районов города по id
     */
    public static function getDistrictBySityId($sity_id) {
        return self::find()->select(['id', 'name'])
                        ->where(['sity_id' => $sity_id])
                        ->asArray()->all();
    }
    /**
     * @return \yii\db\ActiveQuery
     * Полный список районов города по name
     */
    public static function getDistrictByName($sity_id) {
        return self::find()->select(['name', 'id'])
                        ->where(['sity_id' => $sity_id])
                        ->indexBy('id')->column();
    }

}
