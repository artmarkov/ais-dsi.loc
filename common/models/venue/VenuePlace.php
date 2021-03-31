<?php

namespace common\models\venue;

use artsoft\traits\DateTimeTrait;
use Yii;
use artsoft\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\venue\VenueDistrict;
use common\models\venue\VenueCountry;
/**
 * This is the model class for table "venue_place".
 *
 * @property int $id
 * @property int $country_id
 * @property int $sity_id
 * @property int $district_id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property string $phone_optional
 * @property string $email
 * @property string $сontact_person
 * @property double $latitude
 * @property double $longitude
 * @property string $description
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property VenueCountry $country
 * @property VenueDistrict $district
 * @property VenueSity $sity
 * @property User $createdBy
 * @property User $updatedBy
 */
class VenuePlace extends \yii\db\ActiveRecord
{
    public  $map_address;
    use DateTimeTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'venue_place';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'name', 'address', 'phone', 'coords'], 'required'],
            ['district_id', 'required', 'when' => function ($model) { return !empty(VenueDistrict::getDistrictBySityId($model->sity_id));}, 'enableClientValidation' => false ],                 ['district_id', 'required', 'when' => function ($model) { return !empty(VenueDistrict::getDistrictBySityId($model->sity_id));}, 'enableClientValidation' => false ], 
            ['sity_id', 'required', 'when' => function ($model) { return !empty(VenueSity::getSityByCountryId($model->country_id));}, 'enableClientValidation' => false ],         
            [['country_id', 'sity_id', 'district_id', 'map_zoom'], 'integer'],
            ['email', 'email'],
            ['coords', 'string'],
            [['name', 'сontact_person'], 'string', 'max' => 127],
            [['address', 'email', 'description'], 'string', 'max' => 255],
            [['phone', 'phone_optional'], 'string', 'max' => 24],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => VenueCountry::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => VenueDistrict::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['sity_id'], 'exist', 'skipOnError' => true, 'targetClass' => VenueSity::className(), 'targetAttribute' => ['sity_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
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
            'sity_id' => Yii::t('art/guide', 'Sity ID'),
            'district_id' => Yii::t('art/guide', 'District ID'),
            'name' => Yii::t('art/guide', 'Name Place'),
            'address' => Yii::t('art/guide', 'Address'),
            'phone' => Yii::t('art/guide', 'Phone'),
            'phone_optional' => Yii::t('art/guide', 'Phone Optional'),
            'email' => Yii::t('art/guide', 'Email'),
            'сontact_person' => Yii::t('art/guide', 'Contact Person'),
            'coords' => Yii::t('art/guide', 'Coordinates'),
            'description' => Yii::t('art/guide', 'Description Venue'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
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
     */
    public function getDistrict()
    {
        return $this->hasOne(VenueDistrict::className(), ['id' => 'district_id']);
    }

    /* Геттер для названия округа */
    public function getDistrictName()
    {
        return $this->district->name;
    }
    /* Геттер для короткого названия округа */
    public function getDistrictSlug()
    {
        return $this->district->slug;
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
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

}
