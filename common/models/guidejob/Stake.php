<?php

namespace common\models\guidejob;

use Yii;

/**
 * This is the model class for table "teachers_stake".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property TeachersCost[] $teachersCosts
 */
class Stake extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_stake';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            ['status', 'integer'],
            [['name'], 'string', 'max' => 128],
            [['slug'], 'string', 'max' => 32],
            [['name', 'slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'name' => Yii::t('art/teachers', 'Name'),
            'slug' => Yii::t('art/teachers', 'Slug'),
            'status' => Yii::t('art/teachers', 'Status'),
        ];
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
        );
    }

    /**
     * getStatusValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCosts()
    {
        return $this->hasMany(Cost::className(), ['stake_id' => 'id']);
    }

    public static function getStakeList()
    {
        return \yii\helpers\ArrayHelper::map(Stake::find()->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     * Полный список ставок по id
     */
    public static function getStakeByDirectionId($direction_id)
    {
        $data = self::find()
            ->innerJoin('teachers_cost', 'teachers_cost.stake_id = teachers_stake.id')
            ->innerJoin('teachers_direction', 'teachers_direction.id = teachers_cost.direction_id')
            ->select(['teachers_stake.name', 'teachers_stake.id'])
            ->where(['teachers_cost.direction_id' => $direction_id])
            ->asArray()->all();

        return $data;
    }

    /**
     * @return \yii\db\ActiveQuery
     * Полный список ставок по name
     */
    public static function getStakeByName($direction_id)
    {
        $data = self::find()
            ->innerJoin('teachers_cost', 'teachers_cost.stake_id = teachers_stake.id')
            ->innerJoin('teachers_direction', 'teachers_direction.id = teachers_cost.direction_id')
            ->select(['teachers_stake.name as name', 'teachers_stake.id as id'])
            ->where(['teachers_cost.direction_id' => $direction_id])
            ->indexBy('id')->column();

        return $data;
    }
}
