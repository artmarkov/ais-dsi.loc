<?php

namespace common\models\guidesys;

use artsoft\Art;
use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\helpers\AuthHelper;
use artsoft\models\Role;
use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

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
            'attributes' => ['rules_list_read'],
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
        $rules[] = ['youtube_code', 'string', 'max' => 1024];
        $rules[] = ['rules_list_read', 'safe'];
        $rules[] = ['rules_list_read', 'default', 'value' => null];
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
        $attr['youtube_code'] = 'Код YouTube';
        $attr['rules_list_read'] = Yii::t('art/guide', 'Rules Read');
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
        if (User::hasPermission('editHelp') || Yii::$app->user->isSuperadmin) {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public static function getRoles()
    {
        return  Yii::$app->session->get(AuthHelper::SESSION_PREFIX_ROLES, []);
    }
    /**
     * @return array
     */
    public static function getRoleList()
    {
        return ArrayHelper::map(Role::find()
            ->select(['name', 'description'])
            ->asArray()->all(), 'name', 'description');
    }

    /**
     * @return mixed
     */
    public static function getQueryRead()
    {
        $roles = self::getRoles();

        if(Yii::$app->user->isSuperadmin || Art::isBackend()) {
            return self::find()->addOrderBy('root, lft');
        }
        return self::find()->where(
            ['or',
                ['or like', 'rules_list_read', $roles],
                ['and',
                    ['and', ['=', 'rules_list_read', ''], ['!=', 'lvl', 0]],
                    ['in', 'root', self::find()->select('root')->where(
                        ['and', ['or like', 'rules_list_read', $roles], ['=', 'lvl', 0]])->column()
                    ]
                ]
            ])
            ->addOrderBy('root, lft');
    }

}
