<?php

namespace common\models\education;

use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "lesson_progress_teachers_view".
 *
 * @property int|null $teachers_load_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property int|null $plan_year
 * @property int|null $studyplan_id
 * @property int|null $student_id
 */
class LessonProgressTeachersView extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_progress_teachers_view';
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/student', 'Student'),
        ];
    }

    public static function getData($model_date, $teachers_id)
    {
        $data = $dates = [];

        $timestamp_in = Yii::$app->formatter->asTimestamp($model_date->date_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($model_date->date_out) + 86399;

        $attributes = ['studyplan_subject_id' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['student_id' => Yii::t('art/student', 'Student')];

        $lessonDates = LessonItemsProgressTeachersView::find()->select('lesson_date, lesson_items_id')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'teachers_id', $teachers_id])
            ->orderBy('lesson_date')
            ->asArray()->all();
        $modelsProgress = LessonProgressTeachersView::findAll(['teachers_id' => $teachers_id]);

        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.y');
            $attributes += [$date => $label];
            $dates[] = $date;
        }
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['lesson_timestamp'] = $lessonDates;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['teachers_id'] = $modelProgress->teachers_id;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['student_id'] = $modelProgress->student_id;

            $marks = LessonItemsProgressTeachersView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=', 'studyplan_subject_id', $modelProgress->studyplan_subject_id])
                ->all();

           // echo '<pre>' . print_r($marks, true) . '</pre>';
            foreach ($marks as $id => $mark) {
                $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                $data[$item]['lesson_items_id'] = $mark->lesson_items_id;
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

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'teachers_id' => $teachers_id];
    }
}
