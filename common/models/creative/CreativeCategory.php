<?php

namespace common\models\creative;

use artsoft\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "guide_creative_category".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $remark
 *
 * @property CreativeWorks[] $creativeWorks
 */
class CreativeCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_creative_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['parent_id', 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'name' => Yii::t('art', 'Name'),
            'description' => Yii::t('art', 'Description'),
            'parent_id' => Yii::t('art', 'Parent Link'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreativeWorks()
    {
        return $this->hasMany(CreativeWorks::class, ['category_id' => 'id']);
    }

    public static function getCreativeCategoryList()
    {
        $models = \Yii::$app->db->createCommand('SELECT CONCAT(c.name,
                        case
                        when parent_id != 0 then CONCAT(\'(\',(select name from guide_creative_category where id = c.parent_id), \')\')
                        else null
                        end)
                        as name, id
                        FROM guide_creative_category c WHERE c.id not in (select parent_id from guide_creative_category) ORDER BY c.id')
            ->queryAll();
        return  ArrayHelper::map($models, 'id','name');
    }

    public static function getParentCategoryList()
    {
        return  CreativeCategory::find()->select(['name', 'id'])->where(['parent_id' => 0])->orderBy('id')->indexBy('id')->column();
    }

    public static function getCategoryListByParent($parent_id)
    {
        return  CreativeCategory::find()->select(['name', 'id'])->where(['parent_id' => $parent_id])->orderBy('id')->indexBy('id')->column();
    }

    public static function getCategoryListByParentId($parent_id)
    {
        return  CreativeCategory::find()->where(['parent_id' => $parent_id])->orderBy('id')->column();
    }

    public function getParentCategoryName()
    {
        return  CreativeCategory::find()->select('name')->where(['id' => $this->parent_id])->scalar();
    }
}
