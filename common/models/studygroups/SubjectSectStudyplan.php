<?php

namespace common\models\studygroups;

use artsoft\behaviors\ArrayFieldBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subject_sect_studyplan".
 *
 * @property int $id
 * @property int|null $subject_sect_id
 * @property string|null $studyplan_list
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property SubjectSect $subjectSect
 */
class SubjectSectStudyplan extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect_studyplan';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['studyplan_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_sect_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['studyplan_list'], 'string'],
            [['subject_sect_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectSect::className(), 'targetAttribute' => ['subject_sect_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect ID'),
            'studyplan_list' => Yii::t('art/guide', 'Studyplan List'),
            'created_at' => Yii::t('art/guide', 'Created At'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated At'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'version' => Yii::t('art/guide', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[SubjectSect]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSect()
    {
        return $this->hasOne(SubjectSect::className(), ['id' => 'subject_sect_id']);
    }
}
