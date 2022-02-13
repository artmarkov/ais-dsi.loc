<?php

namespace common\models\studyplan;

use artsoft\behaviors\DateFieldBehavior;
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
class StudyplanThematic extends \artsoft\db\ActiveRecord
{
    const THEMATIC_PLAN = 1;
    const REPERTORY_PLAN = 2;

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
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['period_in', 'period_out'],
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'thematic_category', 'template_flag'], 'default', 'value' => null],
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'thematic_category', 'template_flag', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['thematic_category', 'period_in', 'period_out'], 'required'],
            [['period_in', 'period_out'], 'safe'],
            [['template_name'], 'string', 'max' => 256],
            [['template_name'], 'unique'],
            [['template_name'], 'required', 'when' => function ($model) {
                return $model->template_flag == '1';
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"studyplanthematic-template_flag\"]').prop('checked');
                            }"],
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
        return $this->hasMany(StudyplanThematicItems::className(), ['studyplan_thematic_id' => 'id'])->orderBy('id');
    }

    public static function getCategoryList()
    {
        return array(
            self::THEMATIC_PLAN => Yii::t('art/studyplan', 'Thematic Plan'),
            self::REPERTORY_PLAN => Yii::t('art/studyplan', 'Repertory Plan'),
        );
    }

    public static function getCategoryValue($val)
    {
        $ar = self::getCategoryList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if($this->template_flag == 0) {
            $this->template_name = null;
        }
        return parent::beforeSave($insert);
    }
}
