<?php

namespace common\models\guidesys;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * @property int $content_type
 */
class GuidePlanTree extends \kartik\tree\models\Tree
{
    const CATEGORY_SELL = [
        0 => 'Право выбора категории мероприятия',
        1 => 'Внутреннее мероприятие',
        2 => 'Внешнее мероприятие',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_plan_tree';
    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
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
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = ['description', 'string', 'max' => 512];
        $rules[] = ['category_sell', 'integer'];
        $rules[] = ['protocol_flag', 'boolean'];
        $rules[] = ['perform_flag', 'boolean'];
        $rules[] = ['preparing_flag', 'boolean'];
        $rules[] = ['description_flag', 'boolean'];
        $rules[] = ['afisha_flag', 'boolean'];
        $rules[] = ['bars_flag', 'boolean'];
        $rules[] = ['efficiency_flag', 'boolean'];
        $rules[] = ['schedule_flag', 'boolean'];
        $rules[] = ['rider_flag', 'boolean'];
        $rules[] = ['partners_flag', 'boolean'];


        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['description'] = Yii::t('art', 'Description');
        $attr['category_sell'] = 'Категория мероприятия';
        $attr['protocol_flag'] = 'Требуется протокол мероприятия';
        $attr['perform_flag'] = 'Требуется отчет по выполнению плана';
        $attr['preparing_flag'] = 'Требуется подготовка к мероприятию';
        $attr['description_flag'] = 'Требуется описание мероприятия (1000 знаков)';
        $attr['afisha_flag'] = 'Возможность добавления афиши и программы';
        $attr['bars_flag'] = 'Требуется отправлять в БАРС';
        $attr['efficiency_flag'] = 'Требуется подключение показателей эффективности';
        $attr['schedule_flag'] = 'Мероприятие в рамках расписания занятий';
        $attr['rider_flag'] = 'Требуется техническая подготовка';
        $attr['partners_flag'] = 'Возможность участия региональных партнеров';

        return $attr;
    }

    /**
     * @return mixed
     */
    public static function getPlanList()
    {
        return  self::find()->where(['disabled' => false])->select(['name', 'id'])->indexBy('id')->orderBy('name')->column();
    }

    /**
     * @return mixed
     */
    public static function getPlanRoots()
    {
        return  self::find()->roots()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getPlanLiaves()
    {
        return  self::find()->leaves()->select(['root', 'id'])->indexBy('id')->column();
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getCategoryList()
    {
        return self::CATEGORY_SELL;
    }

    /**
     * getStatusValue
     * @param string $val
     * @return string
     */
    public static function getCategoryValue($val)
    {
        $ar = self::getCategoryList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * getStatusValue
     * @param string $val
     * @return string
     */
    public static function getComissionValue($val)
    {
        $ar = self::getComissionList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }
}
