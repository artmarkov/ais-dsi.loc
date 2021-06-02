<?php

namespace common\models\education;

use Yii;

/**
 * This is the model class for table "guide_education_level".
 *
 * @property int $id
 * @property string|null $name
 * @property string $short_name
 * @property int $status
 */
class EducationLevel extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_education_level';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['short_name', 'status'], 'required'],
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['short_name'], 'string', 'max' => 64],
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
            'short_name' => Yii::t('art/guide', 'Short Name'),
            'status' => Yii::t('art/guide', 'Status'),
        ];
    }
}
