<?php

namespace common\models\studyplan;

use artsoft\behaviors\DateFieldBehavior;
use common\models\teachers\Teachers;
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
 * @property int $half_year
 * @property int|null $template_flag
 * @property string|null $template_name
 * @property int $doc_status
 * @property int $doc_sign_teachers_id
 * @property int $doc_sign_timestamp
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
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['thematic_category', 'half_year'], 'required'],
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'thematic_category', 'template_flag'], 'integer'],
            [['doc_status','doc_sign_teachers_id','doc_sign_timestamp', 'half_year'], 'integer'],
            [['half_year'], 'default', 'value' => 0],
            [['template_name'], 'string', 'max' => 256],
            [['template_name'], 'unique'],
            [['template_name'], 'required', 'when' => function ($model) {
                return $model->template_flag == '1';
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"studyplanthematic-template_flag\"]').prop('checked');
                            }"],
            [['doc_sign_teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['doc_sign_teachers_id' => 'id']],

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
            'half_year' => Yii::t('art/guide', 'Half Year'),
            'template_flag' => Yii::t('art/studyplan', 'Template Flag'),
            'template_name' => Yii::t('art/studyplan', 'Template Name'),
            'doc_status' => Yii::t('art/guide', 'Doc Status'),
            'doc_sign_teachers_id' => Yii::t('art/guide', 'Sign Teachers'),
            'doc_sign_timestamp' => Yii::t('art/guide', 'Sign Time'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'doc_sign_teachers_id']);
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
