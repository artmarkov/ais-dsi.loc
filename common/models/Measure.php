<?php

namespace common\models;

use artsoft\eav\models\EavCategories;
use Yii;
use yii\db\ActiveRecord;
use artsoft\eav\EavBehavior;
use artsoft\eav\EavQueryTrait;
/**
 * This is the model class for table "measure".
 *
 * @property int $id
 * @property string $name
 * @property string $abbr
 */
class Measure extends ActiveRecord implements EavCategories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'measure';
    }
    public function behaviors()
    {
        return [
            'eav' => [
                'class' => EavBehavior::className(),
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return  [
                [['name'], 'required'],
                [['category_id'], 'required'],
                [['name'], 'string', 'max' => 64],
                [['abbr'], 'string', 'max' => 32],                
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'category_id' => Yii::t('art', 'Category Id'),
            'name' => Yii::t('art', 'Name'),
            'abbr' => Yii::t('art', 'Abbr'),
        ];
    }
    public function getEavCategories()
    {
        return auth\User::getUserCategoryList();
    }

    public static function getEavCategoryField()
    {
        return 'category_id';
    }

    public function getEavAttributesViewList($model)
    {
        $items = array();
        foreach ($model->getEavAttributes() as $attr) {
            $items[] = array(
                'attribute' => $model->getEavAttribute($attr)->name,
                'label' => $model->getEavAttribute($attr)->label,
                'value' => $model->owner->$attr,
                'filter' => $model->getEavAttribute($model->getEavAttribute($attr)->name)->getEavOptionsList(),
            );
        }
        return $items;
    }

    public function getEavAttributesIndexList($model)
    {
        $items = array();
        foreach ($model->getEavAttributes() as $attr) {
            if($model->getEavAttribute($attr)->visible === 1) {
                $items[] = array(
                    'attribute' => $model->getEavAttribute($attr)->name,
                    'label' => $model->getEavAttribute($attr)->label,
                    
                );
            }
        }
        return $items;
    }
}