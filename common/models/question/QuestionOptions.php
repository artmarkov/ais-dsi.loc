<?php

namespace common\models\question;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "question_options".
 *
 * @property int $id
 * @property int $attribute_id
 * @property string|null $name Название опции атрибута
 * @property int|null $free_flag Свободная строка
 * @property int|null $sort_order
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property QuestionAttribute $attribute0
 */
class QuestionOptions extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question_options';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_id'], 'required'],
            [['attribute_id', 'free_flag', 'sort_order'], 'default', 'value' => null],
            [['attribute_id', 'free_flag', 'sort_order'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionAttribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_id' => 'Attribute ID',
            'name' => 'Название опции атрибута',
            'free_flag' => 'Свободная строка',
            'sort_order' => 'Sort Order',
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Attribute0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttribute0()
    {
        return $this->hasOne(QuestionAttribute::className(), ['id' => 'attribute_id']);
    }

}
