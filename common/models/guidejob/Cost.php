<?php

namespace common\models\guidejob;

use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "teachers_cost".
 *
 * @property int $id
 * @property int $direction_id
 * @property int $stake_id
 * @property double $stake_value
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $version
 *
 * @property TeachersDirection $direction
 * @property TeachersStake $stake
 */
class Cost extends \yii\db\ActiveRecord
{

    use DateTimeTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_cost';
    }

    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direction_id', 'stake_id', 'stake_value'], 'required'],
            [['direction_id', 'stake_id'], 'integer'],
            ['direction_id', 'unique', 'targetAttribute' => ['direction_id', 'stake_id']], // проверка уникальности пары
            [['stake_value'], 'number'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['stake_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stake::class, 'targetAttribute' => ['stake_id' => 'id']],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'direction_id' => Yii::t('art/teachers', 'Direction'),
            'stake_id' => Yii::t('art/teachers', 'Stake'),
            'stake_value' => Yii::t('art/teachers', 'Stake Value'),
            'title' => Yii::t('art', 'Title'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    /* Геттер для названия */
    public function getDirectionName()
    {
        return $this->direction->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStake()
    {
        return $this->hasOne(Stake::class, ['id' => 'stake_id']);
    }

    /* Геттер для названия */
    public function getStakeName()
    {
        return $this->stake->name;
    }

    /* Геттер для названия */
    public function getStakeSlug()
    {
        return $this->stake->slug;
    }

    public static function getCostList()
    {
        return \yii\helpers\ArrayHelper::map(self::find()
            ->innerJoin('guide_teachers_direction', 'guide_teachers_direction.id = teachers_cost.direction_id')
            ->innerJoin('guide_teachers_stake', 'guide_teachers_stake.id = teachers_cost.stake_id')
            ->select('teachers_cost.id as id, guide_teachers_stake.name as name, guide_teachers_direction.name as name_category')
            ->orderBy('teachers_cost.direction_id')
            ->addOrderBy('guide_teachers_stake.id')
            ->asArray()->all(), 'id', 'name', 'name_category');
    }

    /* Геттер получения  direction_id */
    public static function getDirectionId($id)
    {
        if ($id != NULL) {
        $data = self::find()
            ->select(['direction_id'])
            ->where(['id' => $id])->one();

        return $data->direction_id;
    }
    return NULL;
    }

    /* Геттер получения  stake_id */
    public static function getStakeId($id)
    {
         if ($id != NULL) {
        $data = self::find()
            ->select(['stake_id'])
            ->where(['id' => $id])->one();
        return $data->stake_id;
    }
    return NULL;
    }

    /* Геттер получения  id */
    public static function getCostId($direction_id, $stake_id)
    {
        if ($direction_id != NULL && $stake_id != NULL) {
        $data = self::find()
            ->select(['id'])
            ->where(['direction_id' => $direction_id, 'stake_id' => $stake_id])->one();
        return $data->id;
    }
    return NULL;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
