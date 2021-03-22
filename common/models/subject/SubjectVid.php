<?php

namespace common\models\subject;

use Yii;

/**
 * This is the model class for table "subject_category".
 *
 * @property int $id
 * @property int $subject_id
 * @property int $vid_id
 *
 * @property SubjectVidItem $category
 * @property Subject $subject
 */
class SubjectVid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_vid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_id', 'vid_id'], 'required'],
            [['subject_id', 'vid_id'], 'integer'],
            [['vid_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectVidItem::className(), 'targetAttribute' => ['vid_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'subject_id' => Yii::t('art/guide', 'Subject ID'),
            'vid_id' => Yii::t('art/guide', 'Vid ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVid()
    {
        return $this->hasOne(SubjectVidItem::className(), ['id' => 'vid_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }
}
