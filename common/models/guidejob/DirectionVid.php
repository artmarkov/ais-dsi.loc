<?php

namespace common\models\guidejob;

use Yii;

/**
 * This is the model class for table "guide_teachers_direction_vid".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property Cost[] $teachersCosts
 */
class DirectionVid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_teachers_direction_vid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 128],
            [['slug'], 'string', 'max' => 32],
            [['name','slug'], 'unique'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasMany(Teachers::className(), ['direction_vid_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public static function getDirectionVidList()
    {
        return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'name');

    }

    public static function getDirectionVidShortList()
    {
        return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'slug');

    }
}
