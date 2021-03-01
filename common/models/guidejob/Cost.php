<?php

namespace common\models\guidejob;

use Yii;

/**
 * This is the model class for table "{{%teachers_cost}}".
 *
 * @property int $id
 * @property int $direction_id
 * @property int $stake_id
 * @property double $stake
 *
 * @property TeachersDirection $direction
 * @property TeachersStake $stake0
 */
class Cost extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teachers_cost}}';
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
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['stake_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stake::className(), 'targetAttribute' => ['stake_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'direction_id' => Yii::t('art/teachers', 'Direction ID'),
            'stake_id' => Yii::t('art/teachers', 'Stake ID'),
            'stake_value' => Yii::t('art/teachers', 'Stake Value'),
            'title' => Yii::t('art', 'Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
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
        return $this->hasOne(Stake::className(), ['id' => 'stake_id']);
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
        return \yii\helpers\ArrayHelper::map(Cost::find()
            ->innerJoin('teachers_direction', 'teachers_direction.id = teachers_cost.direction_id')
            ->innerJoin('teachers_stake', 'teachers_stake.id = teachers_cost.stake_id')
            ->select('teachers_cost.id as id, teachers_stake.name as name, teachers_direction.name as name_category')
            ->orderBy('teachers_cost.direction_id')
            ->addOrderBy('teachers_stake.id')
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
}
