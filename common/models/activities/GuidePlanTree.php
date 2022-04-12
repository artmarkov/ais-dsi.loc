<?php

namespace common\models\activities;

use Yii;

/**
 * @property int $content_type
 */
class GuidePlanTree extends \kartik\tree\models\Tree
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_plan_tree';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] =  ['description', 'string', 'max' => 512];
        $rules[] =  ['category_flag', 'integer'];
        $rules[] =  ['preparing_flag', 'boolean'];
        $rules[] =  ['description_flag', 'boolean'];
        $rules[] =  ['afisha_flag', 'boolean'];
        $rules[] =  ['bars_flag', 'boolean'];
        $rules[] =  ['efficiency_flag', 'boolean'];
        $rules[] =  ['schedule_flag', 'boolean'];
        $rules[] =  ['consult_flag', 'boolean'];
        $rules[] =  ['partners_flag', 'boolean'];
        $rules[] =  ['commission_flag', 'integer'];


        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['description'] = Yii::t('art', 'Description');
        $attr['category_flag'] = 'Категория мероприятия(внутреннее и (или) внешнее';
        $attr['preparing_flag'] = 'Требуется подготовка к мероприятию' ;
        $attr['description_flag'] = 'Требуется описание мероприятия';
        $attr['afisha_flag'] = 'Требуется афиша и программа';
        $attr['bars_flag'] = 'Требуется отправлять в БАРС';
        $attr['efficiency_flag'] = 'ребуется подключение показателей эффективности';
        $attr['schedule_flag'] = 'Мероприятие в рамках расписания занятий';
        $attr['consult_flag'] = 'Мероприятие в рамках расписания консультаций';
        $attr['partners_flag'] = 'Возможность участия региональных партнеров';
        $attr['commission_flag'] = 'Требуется аттестационная или приемная комиссия';

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
