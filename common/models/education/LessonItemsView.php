<?php

namespace common\models\education;

use Yii;

/**
 * This is the model class for table "lesson_items_view".
 *
 * @property int|null $id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $lesson_date
 * @property string|null $lesson_topic
 * @property string|null $lesson_rem
 * @property int|null $studyplan_subject_id
 * @property int|null $studyplan_id
 * @property int|null $test_category
 * @property string|null $test_name
 * @property string|null $test_name_short
 * @property int|null $plan_flag
 * @property int|null $mark_category
 * @property string|null $mark_label
 * @property string|null $mark_hint
 * @property float|null $mark_value
 * @property string|null $mark_rem
 */
class LessonItemsView extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_items_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'lesson_date' => Yii::t('art/guide', 'Lesson Date'),
            'lesson_topic' => Yii::t('art/guide', 'Lesson Topic'),
            'lesson_rem' => Yii::t('art/guide', 'Lesson Rem'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Studyplan Subject'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'mark_rem' => Yii::t('art/guide', 'Mark Rem'),
            'test_category' => Yii::t('art/guide', 'Test Category'),
            'test_name' => Yii::t('art/guide', 'Test Name'),
            'test_name_short' => Yii::t('art/guide', 'Test Name Short'),
            'plan_flag' => Yii::t('art/guide', 'Plan Flag'),
            'mark_label' => Yii::t('art/guide', 'Mark Label'),
            'mark_hint' => Yii::t('art/guide', 'Mark Hint'),
            'mark_category' => Yii::t('art/guide', 'Mark Category'),
            'mark_value' => Yii::t('art/guide', 'Mark Value'),
        ];
    }
}
