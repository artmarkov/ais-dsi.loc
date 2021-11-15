<?php

namespace common\models\info;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\models\Role;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

class FilesCatalog extends \kartik\tree\models\Tree
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files_catalog_tree';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = ['class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class];
        $behaviors[] = [
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => NULL,
        ];
        $behaviors[] = [
            'class' => BlameableBehavior::class,
            'createdByAttribute' => 'created_by',
            'updatedByAttribute' => NULL,
        ];
        $behaviors[] = [
            'class' => ArrayFieldBehavior::class,
            'attributes' => ['rules_list_read', 'rules_list_edit'],
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['description', 'string', 'max' => 1024];
        $rules[] = ['rules_list_read', 'safe'];
        $rules[] = ['rules_list_read', 'default', 'value' => null];
        $rules[] = ['rules_list_edit', 'safe'];
        $rules[] = ['rules_list_edit', 'default', 'value' => null];
        $rules[] = ['created_at', 'integer'];
        $rules[] = ['created_by', 'integer'];

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['description'] = Yii::t('art', 'Description');
        $attr['rules_list_read'] = Yii::t('art/guide', 'Rules Read');
        $attr['rules_list_edit'] = Yii::t('art/guide', 'Rules Edit');

        return $attr;
    }

    /**
     * @return mixed
     */
    public static function getCatalogList()
    {
        return self::find()->where(['disabled' => false])->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getCatalogRoots()
    {
        return self::find()->roots()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getCatalogLiaves()
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
        if (User::hasPermission('viewCatalog') || Yii::$app->user->isSuperadmin) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public static function getEditAllow()
    {
        $user = \common\models\user\UserCommon::findOne(['user_id' => Yii::$app->user->id]);

        return self::find()->where(['like', 'rules_list_edit', $user->user_category])->scalar() ? true : false;
    }
    /**
     * @return mixed
     */
    public static function getQueryRead()
    {
        $user = \common\models\user\UserCommon::findOne(['user_id' => Yii::$app->user->id]);

        if(!$user || Yii::$app->id == 'backend') {
            return self::find()->addOrderBy('root, lft');
        }
        return self::find()->where(
            ['or',
                ['like', 'rules_list_read', $user->user_category],
                ['and',
                    ['and', ['=', 'rules_list_read', ''], ['!=', 'lvl', 0]],
                    ['in', 'root', self::find()->select('root')->where(
                        ['and', ['like', 'rules_list_read', $user->user_category], ['=', 'lvl', 0]])->column()
                    ]
                ]
            ])
            ->addOrderBy('root, lft');
    }

    /**
     * @return mixed
     */
    public static function getQueryEdit()
    {
        $user = \common\models\user\UserCommon::findOne(['user_id' => Yii::$app->user->id]);

        if(!$user || Yii::$app->id == 'backend') {
            return self::find()->addOrderBy('root, lft');
        }
        return self::find()->where(
            ['or',
                ['like', 'rules_list_edit', $user->user_category],
                ['and',
                    ['and', ['=', 'rules_list_edit', ''], ['!=', 'lvl', 0], ['=', 'created_by', Yii::$app->user->id]],
                    ['in', 'root', self::find()->select('root')->where(
                        ['and', ['like', 'rules_list_edit', $user->user_category], ['=', 'lvl', 0]])->column()
                    ]
                ]
            ])
            ->addOrderBy('root, lft');
    }
}
