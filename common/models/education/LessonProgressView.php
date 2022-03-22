<?php

namespace common\models\education;

use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "lesson_progress_view".
 *
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $programm_id
 * @property int|null $speciality_id
 * @property int|null $course
 * @property int|null $status
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property int|null $subject_sect_studyplan_id
 */
class LessonProgressView extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_progress_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/student', 'Student'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'speciality_id' => Yii::t('art/studyplan', 'Speciality Name'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'status' => Yii::t('art/guide', 'Status'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
        ];
    }
//
//(select count(*) from lesson_items where lesson_items.subject_sect_studyplan_id = 0 and lesson_items.studyplan_subject_id = studyplan_subject.id) as lesson_qty,
//(select count(*) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and (guide_lesson_mark.mark_category = 1 or guide_lesson_mark.mark_category = 2))
//where lesson_items.subject_sect_studyplan_id = 0 and lesson_progress.studyplan_subject_id = studyplan_subject.id) as current_qty,
//(select count(*) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and guide_lesson_mark.mark_category = 3)
//where lesson_items.subject_sect_studyplan_id = 0 and lesson_progress.studyplan_subject_id = studyplan_subject.id) as absence_qty,
//(select avg(mark_value) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id and guide_lesson_test.test_category = 1)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and (guide_lesson_mark.mark_category = 1 or guide_lesson_mark.mark_category = 2) and guide_lesson_mark.mark_value is not null)
//where lesson_items.subject_sect_studyplan_id = 0 and lesson_progress.studyplan_subject_id = studyplan_subject.id) as current_avg_mark,
//(select avg(mark_value) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id and guide_lesson_test.test_category = 2)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and (guide_lesson_mark.mark_category = 1 or guide_lesson_mark.mark_category = 2) and guide_lesson_mark.mark_value is not null)
//where lesson_items.subject_sect_studyplan_id = 0 and lesson_progress.studyplan_subject_id = studyplan_subject.id) as middle_avg_mark,
//(select avg(mark_value) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id and guide_lesson_test.test_category = 3)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and (guide_lesson_mark.mark_category = 1 or guide_lesson_mark.mark_category = 2) and guide_lesson_mark.mark_value is not null)
//where lesson_items.subject_sect_studyplan_id = 0 and lesson_progress.studyplan_subject_id = studyplan_subject.id) as finish_avg_mark
//
//
//(select count(*) from lesson_items where lesson_items.subject_sect_studyplan_id = subject_sect_studyplan.id and lesson_items.studyplan_subject_id = studyplan_subject.id) as lesson_qty,
//(select count(*) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and (guide_lesson_mark.mark_category = 1 or guide_lesson_mark.mark_category = 2))
//where lesson_items.subject_sect_studyplan_id = subject_sect_studyplan.id and lesson_progress.studyplan_subject_id = studyplan_subject.id) as current_qty,
//(select count(*) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and guide_lesson_mark.mark_category = 3)
//where lesson_items.subject_sect_studyplan_id = subject_sect_studyplan.id and lesson_progress.studyplan_subject_id = studyplan_subject.id) as absence_qty,
//(select avg(mark_value) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id and guide_lesson_test.test_category = 1)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and (guide_lesson_mark.mark_category = 1 or guide_lesson_mark.mark_category = 2) and guide_lesson_mark.mark_value is not null)
//where lesson_items.subject_sect_studyplan_id = subject_sect_studyplan.id and lesson_progress.studyplan_subject_id = studyplan_subject.id) as current_avg_mark,
//(select avg(mark_value) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id and guide_lesson_test.test_category = 2)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and (guide_lesson_mark.mark_category = 1 or guide_lesson_mark.mark_category = 2) and guide_lesson_mark.mark_value is not null)
//where lesson_items.subject_sect_studyplan_id = subject_sect_studyplan.id and lesson_progress.studyplan_subject_id = studyplan_subject.id) as middle_avg_mark,
//(select avg(mark_value) from lesson_progress
//inner join lesson_items on (lesson_progress.lesson_items_id = lesson_items.id)
//inner join guide_lesson_test on (guide_lesson_test.id = lesson_items.lesson_test_id and guide_lesson_test.test_category = 3)
//inner join guide_lesson_mark on (guide_lesson_mark.id = lesson_progress.lesson_mark_id and (guide_lesson_mark.mark_category = 1 or guide_lesson_mark.mark_category = 2) and guide_lesson_mark.mark_value is not null)
//where lesson_items.subject_sect_studyplan_id = subject_sect_studyplan.id and lesson_progress.studyplan_subject_id = studyplan_subject.id) as finish_avg_mark
//

    /**
     * @param $model_date
     * @return array
     */
    public static function getData($model_date, $studyplan_id)
    {
        $timestamp_in = Yii::$app->formatter->asTimestamp($model_date->date_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($model_date->date_out) + 86399;

        $lessonDates = LessonItemsProgressTeachersView::find()->select('lesson_date')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'studyplan_id', $studyplan_id])
            ->orderBy('lesson_date')
            ->asArray()->all();

        $modelsProgress = LessonProgressTeachersView::findAll(['studyplan_id' => $studyplan_id]);

        $attributes = ['studyplan_subject_id' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['subject_vid_id' => Yii::t('art/guide', 'Subject Vid')];
        $attributes += ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];

        $dates = [];
        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.y');
            $attributes += [$date => $label];
            $dates[] = $date;
        }
        $data = [];
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;

            $marks = LessonItemsProgressTeachersView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=', 'studyplan_subject_id', $modelProgress->studyplan_subject_id])
                ->all();

            $mark_label = [];
            foreach ($marks as $id => $mark) {
                $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                $mark_label[$date_label][] = $mark['mark_label'] . '[' . $mark['test_name_short'] . ']';

            }
            foreach ($marks as $id => $mark) {
                $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                $data[$item][$date_label] = Editable::widget([
                    'buttonsTemplate' => "{reset}{submit}",
                    'name' => 'lesson_mark_id',
                    'asPopover' => true,
                    'value' => $mark->lesson_mark_id,
                    'header' => $date_label . ' - ' . $mark->test_name,
                    'displayValueConfig' => RefBook::find('lesson_mark')->getList(),
                    'format' => Editable::FORMAT_LINK,
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => RefBook::find('lesson_mark')->getList(),
                    'size' => 'md',
                    'options' => ['class' => 'form-control', 'placeholder' => Yii::t('art', 'Select...')],
                    'formOptions' => [
                        'action' => Url::toRoute(['/studyplan/lesson-progress/set-mark', 'lesson_progress_id' => $mark->lesson_progress_id]),
                    ],
                ]);

            }
        }

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'studyplan_id' => $studyplan_id];
    }
}
