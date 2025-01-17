<?php

namespace common\models\concourse;

use artsoft\models\User;
use himiklab\sortablegrid\SortableGridBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "concourse_criteria".
 *
 * @property int $id
 * @property int $concourse_id
 * @property string|null $name Название критерия
 * @property string|null $name_dev Сокращенное Название критерия
 * @property int|null $sort_order
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property Concourse $concourse
 */
class ConcourseCriteria extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concourse_criteria';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
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
            [['concourse_id', 'name', 'name_dev'], 'required'],
            [['concourse_id', 'sort_order'], 'default', 'value' => null],
            [['concourse_id', 'sort_order'], 'integer'],
            [['name', 'name_dev'], 'string', 'max' => 255],
            [['concourse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Concourse::className(), 'targetAttribute' => ['concourse_id' => 'id']],
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
            'id' => 'ID',
            'concourse_id' => 'Concourse ID',
            'name' => 'Название критерия',
            'name_dev' => 'Сокращенное название критерия',
            'sort_order' => 'Sort Order',
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Concourse]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConcourse()
    {
        return $this->hasOne(Concourse::className(), ['id' => 'concourse_id']);
    }

}
