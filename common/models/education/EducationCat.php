<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use common\models\subject\SubjectType;
use Yii;

/**
 * This is the model class for table "guide_education_cat".
 *
 * @property int $id
 * @property string|null $name
 * @property string $short_name
 * @property string $programm_name
 * @property string $programm_short_name
 * @property string $division_list
 * @property int $status
 *
 * @property EducationProgramm[] $educationProgramms
 */
class EducationCat extends \artsoft\db\ActiveRecord
{

    const BASIS_FREE = 0;
    const BASIS_PAY = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_education_cat';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['division_list'],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name', /*'type_id',*/ 'status', 'programm_short_name'], 'required'],
            [['status'], 'default', 'value' => null],
            [['status', /*'type_id'*/], 'integer'],
            [['name', 'programm_name'], 'string', 'max' => 127],
            [['short_name', 'programm_short_name'], 'string', 'max' => 64],
            [['division_list'], 'safe'],

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
            'short_name' => Yii::t('art', 'Short Name'),
            'programm_name' => 'Название программы',
            'programm_short_name' => 'Сокращенное название программы',
//            'type_id' => Yii::t('art/guide', 'Subject Type Name'),
            'status' => Yii::t('art', 'Status'),
            'division_list' => Yii::t('art/guide', 'Division List'),
        ];
    }

    /**
     * getBasisList
     * @return array
     */
    public static function getBasisList()
    {
        return array(
            self::BASIS_FREE => Yii::t('art/guide', 'Basis Free'),
            self::BASIS_PAY => Yii::t('art/guide', 'Basis Pay'),
        );
    }

    /**
     * Gets query for [[EducationProgramms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationProgramms()
    {
        return $this->hasMany(EducationProgramm::class, ['education_cat_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function getEducationCatList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }
    
}
