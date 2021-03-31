<?php

namespace common\models\venue;

use Yii;

/**
 * This is the model class for table "guide_venue_sity".
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property double $latitude
 * @property double $longitude
 *
 * @property VenueDistrict[] $venueDistricts
 * @property VenuePlace[] $venuePlaces
 * @property VenueCountry $country
 */
class VenueSity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_venue_sity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id'], 'integer'],
            [['name','country_id'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['name'], 'string', 'max' => 64],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => VenueCountry::className(), 'targetAttribute' => ['country_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'country_id' => Yii::t('art/guide', 'Country ID'),
            'name' => Yii::t('art/guide', 'Name Sity'),
            'latitude' => Yii::t('art/guide', 'Latitude'),
            'longitude' => Yii::t('art/guide', 'Longitude'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenueDistricts()
    {
        return $this->hasMany(VenueDistrict::className(), ['sity_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenuePlaces()
    {
        return $this->hasMany(VenuePlace::className(), ['sity_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(VenueCountry::className(), ['id' => 'country_id']);
    }

    /* Геттер для названия страны */
    public function getCountryName()
    {
        return $this->country->name;
    }
    /**
     * @return \yii\db\ActiveQuery
     * Полный список городов
     */
    public static function getVenueSityList()
    {
        return \yii\helpers\ArrayHelper::map(VenueSity::find()->all(), 'id', 'name');
    }
    /**
     * @return \yii\db\ActiveQuery
     * Полный список городов страны по id
     */
    public static function getSityByCountryId($country_id) { 
     $data = self::find()->select(['id','name']) 
     ->where(['country_id'=>$country_id]) 
     ->asArray()->all(); 

      return $data; 
     }  
    /**
     * @return \yii\db\ActiveQuery
     * Полный список городов страны по name
     */     
     public static function getSityByName($country_id) { 
     $data = self::find()->select(['name','id']) 
     ->where(['country_id'=>$country_id]) 
     ->indexBy('id')->column(); 

      return $data; 
     } 
}
