<?php

namespace common\models\guidejob;

use artsoft\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "guide_teachers_direction".
 *
 * @property int $id
 * @property int $parent
 * @property string $name
 * @property string $slug
 *
 * @property Cost[] $Costs
 * @property Cost[] $parentsDependencies
 */
class Direction extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_teachers_direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['parent'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['slug'], 'string', 'max' => 32],
            [['name', 'slug'], 'unique'],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['parent' => 'id']],
            ['parent', function ($attribute, $params) {
                if ($this->$attribute == $this->id) $this->addError($attribute, 'Невозможно наследовать свойства исходного объекта.');
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'parent' => Yii::t('art/teachers', 'Parent Вependencies'),
            'name' => Yii::t('art/teachers', 'Name'),
            'slug' => Yii::t('art/teachers', 'Slug'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCosts()
    {
        return $this->hasMany(Cost::class, ['direction_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentsDependencies()
    {
        return $this->hasMany(Direction::class, ['parent' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public static function getDirectionList()
    {
        return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'name');

    }

    public static function getParentName($id)
    {
        return $id ? self::find($id)->select('name')->scalar() : null;
    }
}
