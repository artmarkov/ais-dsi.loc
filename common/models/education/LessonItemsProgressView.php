<?php

namespace common\models\education;

use Yii;

/**
 * This is the model class for table "lesson_items_progress_view".
 *
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int|null $studyplan_id
 * @property int|null $subject_sect_id
 * @property int|null $lesson_items_id
 * @property int|null $lesson_date
 * @property string|null $lesson_topic
 * @property string|null $lesson_rem
 * @property int|null $lesson_progress_id
 * @property int|null $lesson_mark_id
 * @property int|null $test_category
 * @property string|null $test_name
 * @property string|null $test_name_short
 * @property int|null $plan_flag
 * @property int|null $mark_category
 * @property string|null $mark_label
 * @property string|null $mark_hint
 * @property float|null $mark_value
 * @property string|null $mark_rem
 * @property string|null $teachers_list
 */
class LessonItemsProgressView extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_items_progress_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Studyplan Subject ID'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan ID'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect ID'),
            'lesson_items_id' => Yii::t('art/guide', 'Lesson Items ID'),
            'lesson_date' => Yii::t('art/guide', 'Lesson Date'),
            'lesson_topic' => Yii::t('art/guide', 'Lesson Topic'),
            'lesson_rem' => Yii::t('art/guide', 'Lesson Rem'),
            'lesson_progress_id' => Yii::t('art/guide', 'Lesson Progress ID'),
            'lesson_mark_id' => Yii::t('art/guide', 'Lesson Mark ID'),
            'test_category' => Yii::t('art/guide', 'Test Category'),
            'test_name' => Yii::t('art/guide', 'Test Name'),
            'test_name_short' => Yii::t('art/guide', 'Test Name Short'),
            'plan_flag' => Yii::t('art/guide', 'Plan Flag'),
            'mark_category' => Yii::t('art/guide', 'Mark Category'),
            'mark_label' => Yii::t('art/guide', 'Mark Label'),
            'mark_hint' => Yii::t('art/guide', 'Mark Hint'),
            'mark_value' => Yii::t('art/guide', 'Mark Value'),
            'mark_rem' => Yii::t('art/guide', 'Mark Rem'),
            'teachers_list' => Yii::t('art/guide', 'Teachers List'),
        ];
    }
}
