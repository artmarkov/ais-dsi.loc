<?php

namespace common\models\auditory;

use Yii;

/**
 * This is the model class for table "guide_auditory_cat".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 */
class AuditoryCat extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_auditory_cat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name Auditory Category'),
            'description' => Yii::t('art/guide', 'Description Auditory Category'),
        ];
    }
    public function getAuditory()
    {
        return $this->hasMany(Auditory::className(), ['cat_id' => 'id']);
        
    }

    public static function getAuditoryCatList()
    {
      return  AuditoryCat::find()->select(['name', 'id'])->indexBy('id')->column();
    }
   
}
