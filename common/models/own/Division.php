<?php

namespace common\models\own;

use Yii;

/**
 * This is the model class for table "division".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 */
class Division extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'division';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['name'], 'string', 'max' => 127],
            [['slug'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name'),
            'slug' => Yii::t('art/guide', 'Slug'),
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */

    public static function getDivisionList()
    {
        return \yii\helpers\ArrayHelper::map(Division::find()->all(), 'id', 'name');

    }
}
