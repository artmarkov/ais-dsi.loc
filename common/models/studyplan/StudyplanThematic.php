<?php

namespace common\models\studyplan;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "studyplan_thematic".
 *
 * @property int $id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int $thematic_category
 * @property int $period_in
 * @property int $period_out
 * @property int|null $template_flag
 * @property string|null $template_name
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property StudyplanThematicItems[] $studyplanThematicItems
 */
class StudyplanThematic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_thematic';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'thematic_category', 'period_in', 'period_out', 'template_flag'], 'default', 'value' => null],
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'thematic_category', 'period_in', 'period_out', 'template_flag', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['thematic_category', 'period_in', 'period_out'], 'required'],
            [['template_name'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/studyplan', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect_Name'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'thematic_category' => Yii::t('art/studyplan', 'Thematic Category'),
            'period_in' => Yii::t('art/studyplan', 'Period In'),
            'period_out' => Yii::t('art/studyplan', 'Period Out'),
            'template_flag' => Yii::t('art/studyplan', 'Template Flag'),
            'template_name' => Yii::t('art/studyplan', 'Template Name'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[StudyplanThematicItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanThematicItems()
    {
        return $this->hasMany(StudyplanThematicItems::className(), ['studyplan_thematic_id' => 'id']);
    }
}
