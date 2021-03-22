<?php

namespace common\models\subject;

use Yii;

/**
 * This is the model class for table "subject_category".
 *
 * @property int $id
 * @property int $subject_id
 * @property int $category_id
 *
 * @property SubjectCategoryItem $category
 * @property Subject $subject
 */
class SubjectCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_id', 'category_id'], 'required'],
            [['subject_id', 'category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategoryItem::className(), 'targetAttribute' => ['category_id' => 'id']],
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
            'category_id' => Yii::t('art/guide', 'Category ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(SubjectCategoryItem::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }
}
