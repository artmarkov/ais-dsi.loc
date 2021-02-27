<?php

namespace common\models\service;

use Yii;
use artsoft\widgets\ActiveForm;
/**
 * @property int $content_type
 */
class TreeItem extends \kartik\tree\models\Tree
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tree_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        
           $rules[] =  ['content_type', 'integer', 'min' => 0];
           $rules[] =  ['content_type', 'default', 'value' => 0];
            
       return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
         $attr = parent::attributeLabels();        
         $attr['content_type'] = Yii::t('art/service', 'Content Type');
        
        return $attr;
    }
    
    public static function getTypeList() {
        return [
            'категория',
            'товар',
        ];
        
    }
}
