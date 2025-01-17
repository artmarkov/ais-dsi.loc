<?php

namespace common\models\concourse;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "concourse_item".
 *
 * @property int $id
 * @property int $concourse_id
 * @property string|null $authors_list Авторы
 * @property string|null $name Название работы
 * @property string|null $description Описание работы
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property Concourse $concourse
 * @property ConcourseValue[] $concourseValues
 */
class ConcourseItem extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concourse_item';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['authors_list'],
            ],
            [
                'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concourse_id', 'authors_list'], 'required'],
            [['concourse_id'], 'default', 'value' => null],
            [['concourse_id'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['description'], 'string'],
            [['authors_list'], 'safe'],
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
            'authors_list' => 'Авторы',
            'name' => 'Название работы',
            'description' => 'Описание работы',
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


    /**
     * Gets query for [[ConcourseValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConcourseValues()
    {
        return $this->hasMany(ConcourseValue::className(), ['concourse_item_id' => 'id']);
    }
}
