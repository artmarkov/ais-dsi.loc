<?php

namespace common\models\entrant;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "entrant_group".
 *
 * @property int $id
 * @property int $comm_id
 * @property string|null $name Название группы
 * @property int|null $prep_flag С подготовкой/Без подготовки
 * @property int $timestamp_in Время испытания
 * @property string|null $description Описание группы
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 * @property int $group_secretary_id
 * @property int $group_leader_id
 * @property int $group_soleader_id
 * @property string $group_members_list
 *
 * @property Entrant[] $entrants
 * @property EntrantComm $comm
 */
class EntrantGroup extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comm_id', 'timestamp_in', 'prep_flag', 'name'], 'required'],
            [['comm_id', 'prep_flag', 'version', 'group_secretary_id', 'group_leader_id', 'group_soleader_id'], 'integer'],
            [['timestamp_in'], 'safe'],
            [['prep_flag'], 'default', 'value' => 0],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
            [['group_members_list'], 'safe'],
            [['comm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntrantComm::className(), 'targetAttribute' => ['comm_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['group_members_list'],
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['timestamp_in'],
                'timeFormat' => 'd.m.Y H:i'
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'comm_id' => Yii::t('art/guide', 'Comm ID'),
            'name' => Yii::t('art/guide', 'Group Name'),
            'prep_flag' => Yii::t('art/guide', 'Prep Flag'),
            'timestamp_in' => Yii::t('art/guide', 'Group Timestamp'),
            'description' => Yii::t('art', 'Description'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'group_secretary_id' => Yii::t('art/guide', 'Secretary'),
            'group_members_list' => Yii::t('art/guide', 'Members List'),
            'group_leader_id' => Yii::t('art/guide', 'Leader'),
            'group_soleader_id' => 'Заместитель председателя',
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Entrants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrants()
    {
        return $this->hasMany(Entrant::className(), ['group_id' => 'id']);
    }

    /**
     * Gets query for [[Comm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComm()
    {
        return $this->hasOne(EntrantComm::className(), ['id' => 'comm_id']);
    }

    public static function getPrepList()
    {
        return array(
            1 => 'С подготовкой',
            0 => 'Без подготовки',
        );
    }

    public static function getPrepValue($val)
    {
        $ar = self::getPrepList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

}
