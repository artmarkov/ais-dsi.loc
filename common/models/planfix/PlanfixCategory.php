<?php

namespace common\models\planfix;

use Yii;
use artsoft\db\ActiveRecord;

/**
 * This is the model class for table "guide_planfix_category".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $roles
 *
 * @property Planfix[] $planfixes
 */
class PlanfixCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_planfix_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'roles'], 'required'],
            [['name'], 'string', 'max' => 256],
            [['description', 'roles'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'roles' => 'Roles',
        ];
    }

    /**
     * Gets query for [[Planfixes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlanfixes()
    {
        return $this->hasMany(Planfix::className(), ['category_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function getPlanfixCategoryList()
    {
        return  self::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getPlanfixCategoryValue($val)
    {
        $ar = self::getPlanfixCategoryList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }
}
