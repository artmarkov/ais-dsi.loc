<?php

namespace common\models\auditory;

use himiklab\sortablegrid\SortableGridBehavior;
use artsoft\db\ActiveRecord;
use Yii;
use artsoft\traits\DateTimeTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auditory".
 *
 * @property int $id
 * @property int $building_id
 * @property int $cat_id
 * @property int $num
 * @property string $name
 * @property string $slug
 * @property string $floor
 * @property double $area
 * @property int $capacity
 * @property string $description
 * @property int $order
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $status
 * @property int $version
 */
class Auditory extends ActiveRecord
{
    use DateTimeTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditory';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            'grid-sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'num', 'building_id'], 'required'],
            [['id', 'building_id', 'cat_id', 'num', 'capacity', 'sort_order', 'version'], 'integer'],
            [['area'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['floor'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 255],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', '#'),
            'building_id' => Yii::t('art/guide', 'Building ID'),
            'cat_id' => Yii::t('art/guide', 'Cat ID'),
            'num' => Yii::t('art/guide', 'Num Auditory'),
            'name' => Yii::t('art/guide', 'Name Auditory'),
            'floor' => Yii::t('art/guide', 'Floor'),
            'area' => Yii::t('art/guide', 'Area Auditory'),
            'capacity' => Yii::t('art/guide', 'Capacity Auditory'),
            'description' => Yii::t('art/guide', 'Description Auditory'),
            'sort_order' => Yii::t('art/guide', 'Order'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
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

    public static function getAuditoryList()
    {
        return Auditory::find()->select(['CONCAT(num,\' - \',name) as name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(self::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(self::class, ['id' => 'updated_by']);
    }
}
