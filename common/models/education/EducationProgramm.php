<?php

namespace common\models\education;

use Yii;

/**
 * This is the model class for table "education_programm".
 *
 * @property int $id
 * @property int $education_cat_id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $speciality_list
 * @property int|null $period_study
 * @property string|null $description
 * @property string|null $category_list
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property GuideEducationCat $educationCat
 */
class EducationProgramm extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_programm';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['education_cat_id', 'created_at', 'updated_at'], 'required'],
            [['education_cat_id', 'period_study', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'default', 'value' => null],
            [['education_cat_id', 'period_study', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['slug'], 'string', 'max' => 32],
            [['speciality_list', 'description', 'category_list'], 'string', 'max' => 1024],
            [['education_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationCat::className(), 'targetAttribute' => ['education_cat_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'education_cat_id' => Yii::t('art/guide', 'Education Cat ID'),
            'name' => Yii::t('art/guide', 'Name'),
            'slug' => Yii::t('art/guide', 'Slug'),
            'speciality_list' => Yii::t('art/guide', 'Speciality List'),
            'period_study' => Yii::t('art/guide', 'Period Study'),
            'description' => Yii::t('art/guide', 'Description'),
            'category_list' => Yii::t('art/guide', 'Category List'),
            'created_at' => Yii::t('art/guide', 'Created At'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated At'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'status' => Yii::t('art/guide', 'Status'),
            'version' => Yii::t('art/guide', 'Version'),
        ];
    }

    /**
     * Gets query for [[EducationCat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationCat()
    {
        return $this->hasOne(EducationCat::className(), ['id' => 'education_cat_id']);
    }
}
