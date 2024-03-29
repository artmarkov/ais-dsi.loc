<?php

namespace common\models\guidesys;

use artsoft\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "guide_user_relation".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property UserFamily[] $userFamilies
 */
class UserRelation extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_user_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['name'], 'string', 'max' => 127],
            [['slug'], 'string', 'max' => 64],
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
    public function getUserFamilies()
    {
        return $this->hasMany(UserFamily::class, ['relation_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     * Полный список Отношений в семье
     */
    public static function getRelationList()
    {
        return \yii\helpers\ArrayHelper::map(UserRelation::find()->all(), 'id', 'name');
    }
}
