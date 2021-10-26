<?php

namespace common\models\venue;

use Yii;

/**
 * This is the model class for table "guide_venue_country".
 *
 * @property int $id
 * @property string $name
 * @property string $fullname
 * @property string $alpha2
 * @property string $alpha3
 *
 * @property VenuePlace[] $venuePlaces
 * @property VenueSity[] $venueSities
 */
class VenueCountry extends \artsoft\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_venue_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'fullname', 'alpha2', 'alpha3'], 'required'],
            [['name', 'fullname'], 'string', 'max' => 100],
            [['alpha2'], 'string', 'max' => 2],
            [['alpha3'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name Country'),
            'fullname' => Yii::t('art/guide', 'Fullname'),
            'alpha2' => Yii::t('art/guide', 'Alpha2'),
            'alpha3' => Yii::t('art/guide', 'Alpha3'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenuePlaces()
    {
        return $this->hasMany(VenuePlace::className(), ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenueSities()
    {
        return $this->hasMany(VenueSity::className(), ['country_id' => 'id']);
    }

    public static function getVenueCountryList()
    {
        return \yii\helpers\ArrayHelper::map(VenueCountry::find()->where(['not', ['id' => 0]])->all(), 'id', 'name');

    }

}
