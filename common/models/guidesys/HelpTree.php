<?php

namespace common\models\guidesys;

use Yii;

class HelpTree extends \kartik\tree\models\Tree
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_help_tree';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = ['class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class];
        return $behaviors;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] =  ['description', 'string', 'max' => 1024];

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['description'] = Yii::t('art', 'Description');

        return $attr;
    }

    /**
     * @return mixed
     */
    public static function getEfficiencyList()
    {
        return  self::find()->where(['disabled' => false])->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getEfficiencyRoots()
    {
        return  self::find()->roots()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getEfficiencyLiaves()
    {
        return  self::find()->leaves()->select(['root', 'id'])->indexBy('id')->column();
    }
}
