<?php

namespace common\models\guidesys;

use artsoft\models\User;
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
        $rules[] = ['description', 'string', 'max' => 1024];

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
    public static function getHelpList()
    {
        return self::find()->where(['disabled' => false])->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getHelpRoots()
    {
        return self::find()->roots()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getHelpLiaves()
    {
        return self::find()->leaves()->select(['root', 'id'])->indexBy('id')->column();
    }

    /**
     * Override isReadonly method if you need as shown in the
     * example below. You can override similarly other methods
     * like isActive, isMovable etc.
     */
    public function isReadonly()
    {
        if(Yii::$app->id == 'frontend') {
            return true;
        }
        if (User::hasPermission('editHelp') || Yii::$app->user->isSuperadmin) {
            return false;
        }
        return true;
    }

}
