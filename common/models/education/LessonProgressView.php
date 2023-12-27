<?php

namespace common\models\education;

use artsoft\Art;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\guidejob\Direction;
use common\models\studyplan\Studyplan;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersLoadView;
use common\widgets\editable\Editable;
use Yii;
use yii\db\Query;
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

        if ($model_date->subject_sect_studyplan_id == '') {
            $lessonDates = [];
            $modelsProgress = [];
            $modelsMarks = [];
        } else {
            $lessonDates = LessonItemsProgressView::find()->select('lesson_date, test_name_short')->distinct()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=', 'subject_sect_id', $subject_sect_id])
                ->andWhere(['=', 'subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id])
                ->orderBy('lesson_date')
                ->asArray()->all();
            $modelsProgress = self::find()->where(['subject_sect_studyplan_id' => $model_date->subject_sect_studyplan_id])
                ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
                ->orderBy('sect_name')->all();

            $modelsMarks = ArrayHelper::index(LessonItemsProgressView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['subject_sect_id' => $subject_sect_id])
                ->andWhere(['=', 'subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id])
                ->all(), null, 'studyplan_subject_id');
        }

        // echo '<pre>' . print_r($modelsMarks, true) . '</pre>'; die();
        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d') . ' ' . $lessonDate['test_name_short'];
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

    public static function getDataStudyplan($model_date, $studyplan_id, $readonly = false)
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

            if (isset($modelsMarks[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarks[$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                    $data[$item][$date_label] = !$readonly ? self::getEditableForm($date_label, $mark) : $mark->mark_label;
                }
            }
        }

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes];
    }

    public static function getDataTeachers($model_date, $teachers_id, $plan_year)
    {
        $data = $dates = $modelsProgress = [];

        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];

        $attributes = ['studyplan_subject_id' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['student_id' => Yii::t('art/student', 'Student')];

        if ($model_date->subject_sect_studyplan_id != 0) {
            $lessonDates = LessonItemsProgressView::find()->select('lesson_date, test_name_short')->distinct()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
                ->andWhere(['=', 'subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id])
                ->orderBy('lesson_date')
                ->asArray()->all();
            $modelsProgress = self::find()
                ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
                ->andWhere(['=', 'subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id])
                ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
                ->andWhere(['plan_year' => $plan_year])
                ->all();
            foreach ($lessonDates as $id => $lessonDate) {
                $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
                $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d') . ' ' . $lessonDate['test_name_short'];
                $attributes += [$date => $label];
                $dates[] = $date;
            }
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
//            $data[$item]['timestamp_in'] = $timestamp_in;

            $marks = LessonItemsProgressView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=', 'studyplan_subject_id', $modelProgress->studyplan_subject_id])
                ->all();

            foreach ($marks as $id => $mark) {
                $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                $data[$item][$date_label] = self::getEditableForm($date_label, $mark, $teachers_id);
            }
        }

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes];
    }


    public static function getSectListForTeachersQuery($teachers_id, $plan_year)
    {
        return self::find()
            ->select('subject_sect_studyplan_id, sect_name, subject')
            ->distinct()
            ->where(['!=', 'subject_sect_studyplan_id', 0])
            ->andWhere(['plan_year' => $plan_year])
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->orderBy('sect_name');

    }

    public static function getSectListForTeachers($teachers_id, $plan_year)
    {
        return ArrayHelper::map(self::getSectListForTeachersQuery($teachers_id, $plan_year)->all(), 'subject_sect_studyplan_id', 'sect_name', 'subject');
    }

    public static function getSecListForTeachersDefault($teachers_id, $plan_year)
    {
        $model = self::getSectListForTeachersQuery($teachers_id, $plan_year)->one();
        return $model->subject_sect_studyplan_id ?? null;
    }

    public static function getDataIndivTeachers($model_date, $teachers_id, $plan_year)
    {
        $data = $dates = $datesLoad = [];

        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];

        $attributes = ['studyplan_subject_id' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['student_id' => Yii::t('art/student', 'Student')];

        $lessonDates = LessonItemsProgressView::find()->select('lesson_date, test_name_short')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->andWhere(['=', 'subject_key', $model_date->subject_key])
            ->orderBy('lesson_date')
            ->asArray()->all();
        $modelsProgress = self::find()
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->andWhere(['=', 'subject_key', $model_date->subject_key])
            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
            ->andWhere(['plan_year' => $plan_year])
            ->all();

        $studyplanSubjectIds = ArrayHelper::getColumn($modelsProgress, 'studyplan_subject_id');
        $dates_load_total = 0;
        foreach ($lessonDates as $id => $lessonDate) {
            $dates_load = 0;
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d') . ' ' . $lessonDate['test_name_short'];
            $attributes += [$date => $label];
            if (Art::isBackend()) {
                $datesArray = (new Query())->from('activities_schedule_view')
                    ->innerJoin('lesson_items', 'lesson_items.subject_sect_studyplan_id = activities_schedule_view.subject_sect_studyplan_id AND lesson_items.studyplan_subject_id = activities_schedule_view.studyplan_subject_id')
                    ->innerJoin('lesson_progress', 'lesson_progress.lesson_items_id = lesson_items.id')
                    ->select(new \yii\db\Expression('datetime_out - datetime_in AS time'))
                    ->where(['in', 'activities_schedule_view.studyplan_subject_id', $studyplanSubjectIds])
                    ->andWhere(['and', ['>=', 'datetime_in', $lessonDate['lesson_date']], ['<', 'datetime_in', $lessonDate['lesson_date'] + 86400]])
                    ->andWhere(['and', ['>=', 'lesson_date', $lessonDate['lesson_date']], ['<', 'lesson_date', $lessonDate['lesson_date'] + 86400]])
                    ->andWhere(['=', 'direction_id', 1000])
                    ->andWhere(['IS NOT', 'lesson_mark_id', NULL])
                    ->column();
//                print_r($datesArray); die();
                foreach ($datesArray as $index => $time) {
                    $dates_load += Schedule::astr2academ($time);
                }
                $dates_load_total += $dates_load;
            }
            $dates[] = ['date' => $date, 'dates_load' => $dates_load];
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
            $data[$item]['subject_key'] = $modelProgress->subject_key;
            $data[$item]['subject'] = $modelProgress->subject;
            $data[$item]['timestamp_in'] = $timestamp_in;

            $marks = LessonItemsProgressView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=', 'studyplan_subject_id', $modelProgress->studyplan_subject_id])
                ->all();

            foreach ($marks as $id => $mark) {
                $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                $data[$item][$date_label] = self::getEditableForm($date_label, $mark, $teachers_id);
            }
        }

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'dates_load_total' => $dates_load_total];
    }

    public static function getIndivListForTeachersQuery($teachers_id, $plan_year)
    {
        return self::find()
            ->select('subject_key, subject')
            ->distinct()
            ->where(['is not', 'subject_key', NULL])
            ->andWhere(['status' => Studyplan::STATUS_ACTIVE])
            ->andWhere(['plan_year' => $plan_year])
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->orderBy('subject');

    }

    public static function getIndivListForTeachers($teachers_id, $plan_year)
    {
        return ArrayHelper::map(self::getIndivListForTeachersQuery($teachers_id, $plan_year)->all(), 'subject_key', 'subject');
    }

    public static function getIndivListForTeachersDefault($teachers_id, $plan_year)
    {
        $model = self::getIndivListForTeachersQuery($teachers_id, $plan_year)->one();
        return $model->subject_key ?? null;
    }

    /**
     * @param $date_label
     * @param $mark
     * @return string
     * @throws \Exception
     */
    protected static function getEditableForm($date_label, $mark, $teachers_id = null)
    {
        $mark_list = LessonMark::getMarkLabelForStudent([LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);

        return Editable::widget([
            'buttonsTemplate' => "{reset}{submit}",
            'name' => 'lesson_mark_id',
            'asPopover' => true,
            'disabled' => $teachers_id !== null ? (\artsoft\Art::isFrontend() && !Teachers::isOwnTeacher($teachers_id)) : false,
            'value' => $mark->lesson_mark_id,
            'header' => $date_label . ' - ' . $mark->test_name,
            'displayValueConfig' => $mark_list,
            'format' => Editable::FORMAT_LINK,
            'inputType' => Editable::INPUT_DROPDOWN_LIST,
            'data' => $mark_list,
            'size' => 'md',
            'options' => ['class' => 'form-control', 'placeholder' => Yii::t('art', 'Select...')],
            'formOptions' => [
                'action' => Url::toRoute(['/studyplan/lesson-progress/set-mark', 'lesson_progress_id' => $mark->lesson_progress_id]),
            ],
        ]);
    }
}
