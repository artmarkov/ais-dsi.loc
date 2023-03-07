<?php

namespace common\models\education;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "lesson_progress_view".
 *
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $subject_sect_id
 * @property int|null $plan_year
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property string|null $teachers_list
 * @property string|null $sect_name
 * @property string|null $subject
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
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect ID'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/student', 'Student'),
            'teachers_list' => Yii::t('art/teachers', 'Teachers'),
            'sect_name' => Yii::t('art/guide', 'Sect Name'),
            'subject' => Yii::t('art/guide', 'Subject'),
        ];
    }

    public static function getDataSect($model_date, $subject_sect_id)
    {
        $data = $dates = [];
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];

        $attributes = ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['student_id' => Yii::t('art/student', 'Student')];

        $lessonDates = LessonItemsProgressView::find()->select('lesson_date')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'subject_sect_id', $subject_sect_id])
            ->orderBy('lesson_date')
            ->asArray()->all();
        $modelsProgress = self::find()->where(['subject_sect_id' => $subject_sect_id])->orderBy('sect_name')->all();

        $modelsMarks = ArrayHelper::index(LessonItemsProgressView::find()
                        ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                        ->andWhere(['subject_sect_id' => $subject_sect_id])
                        ->all(), null, 'studyplan_subject_id');

        // echo '<pre>' . print_r($modelsMarks, true) . '</pre>'; die();
        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d');
            $attributes += [$date => $label];
            $dates[] = $date;
        }
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['lesson_timestamp'] = $lessonDates;
            $data[$item]['subject_sect_id'] = $modelProgress->subject_sect_id;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['sect_name'] = $modelProgress->sect_name;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['student_id'] = $modelProgress->student_id;
            $data[$item]['student_fio'] = $modelProgress->student_fio;

            if (isset($modelsMarks[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarks[$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                    $data[$item][$date_label] = self::getEditableForm($date_label, $mark);
                }
            }
        }

        // echo '<pre>' . print_r($data, true) . '</pre>';
        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes];
    }

    public static function getDataStudyplan($model_date, $studyplan_id)
    {
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];
        $lessonDates = LessonItemsProgressView::find()->select('lesson_date')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'studyplan_id', $studyplan_id])
            ->orderBy('lesson_date')
            ->asArray()->all();

        $modelsProgress = self::findAll(['studyplan_id' => $studyplan_id]);

        $modelsMarks = ArrayHelper::index(LessonItemsProgressView::find()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['studyplan_id' => $studyplan_id])
            ->all(), null, 'studyplan_subject_id');

        $attributes = ['studyplan_subject_id' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['subject_vid_id' => Yii::t('art/guide', 'Subject Vid')];
        $attributes += ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];

        $dates = [];
        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d');
            $attributes += [$date => $label];
            $dates[] = $date;
        }
        $data = [];
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['lesson_timestamp'] = $lessonDates;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['sect_name'] = $modelProgress->sect_name;
            $data[$item]['subject'] = $modelProgress->subject;

            $marks = LessonItemsProgressView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=', 'studyplan_subject_id', $modelProgress->studyplan_subject_id])
                ->all();

            if (isset($modelsMarks[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarks[$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                    $data[$item][$date_label] = self::getEditableForm($date_label, $mark);
                }
            }
        }

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes];
    }

    public static function getDataTeachers($model_date, $teachers_id)
    {
        $data = $dates = [];

        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];

        $attributes = ['studyplan_subject_id' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['student_id' => Yii::t('art/student', 'Student')];

        $lessonDates = LessonItemsProgressView::find()->select('lesson_date')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->orderBy('lesson_date')
            ->asArray()->all();
        $modelsProgress = self::find()
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->all();;

        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d');
            $attributes += [$date => $label];
            $dates[] = $date;
        }
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['lesson_timestamp'] = $lessonDates;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['teachers_id'] = $teachers_id;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['student_id'] = $modelProgress->student_id;
            $data[$item]['sect_name'] = $modelProgress->sect_name;
            $data[$item]['student_fio'] = $modelProgress->student_fio;
            $data[$item]['subject'] = $modelProgress->subject;

            $marks = LessonItemsProgressView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=', 'studyplan_subject_id', $modelProgress->studyplan_subject_id])
                ->all();

            foreach ($marks as $id => $mark) {
                $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                $data[$item][$date_label] = self::getEditableForm($date_label, $mark);
            }
        }

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes];
    }

    /**
     * @param $date_label
     * @param $mark
     * @return string
     * @throws \Exception
     */
    protected static function getEditableForm($date_label, $mark)
    {
        return Editable::widget([
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
