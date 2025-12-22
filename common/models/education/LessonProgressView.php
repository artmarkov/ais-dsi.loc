<?php

namespace common\models\education;

use artsoft\Art;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\Schedule;
use common\models\studyplan\Studyplan;
use common\models\teachers\Teachers;
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
        $mark_list = LessonMark::getMarkLabelForStudent([LessonMark::PRESENCE, LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);

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
                ->andWhere(['OR',
                    ['status' => Studyplan::STATUS_ACTIVE],
                    ['AND',
                        ['status' => Studyplan::STATUS_INACTIVE],
                        ['status_reason' => [1, 2, 4]]
                    ]
                ])
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
            $data[$item]['student_fio'] = $modelProgress->student_fullname;

            if (isset($modelsMarks[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarks[$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                    $data[$item][$date_label] = self::getEditableForm($mark_list, $mark);
                }
            }
        }

        // echo '<pre>' . print_r($data, true) . '</pre>';
        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes];
    }

    public static function getDataStudyplan($model_date, $studyplan_id, $readonly = false)
    {
        $mark_list = LessonMark::getMarkLabelForStudent([LessonMark::PRESENCE, LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);
        $mark_list_sertif = LessonMark::getMarkLabelForStudent([LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);

        $timestamp = ArtHelper::getMonYearParamsFromArray([$model_date->date_in, $model_date->date_out]);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];
        $plan_year = ArtHelper::getStudyYearDefault(null, $timestamp_in);

        $lessonDates = LessonItemsProgressView::find()->select('lesson_date')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'studyplan_id', $studyplan_id])
            ->andWhere(['=', 'test_category', 1])
            ->orderBy('lesson_date')
            ->asArray()->all();

        /*$lessonCertifLabel = LessonItemsProgressView::find()->select('lesson_test_id, test_name_short')->distinct()
            ->where(['plan_year' => $plan_year])
            ->andWhere(['studyplan_id' => $studyplan_id])
            ->andWhere(['!=', 'test_category', 1])
            ->asArray()->all();*/

        $modelsProgress = self::find()->where(['studyplan_id' => $studyplan_id])->all();
        $studyplanSubjectIds = ArrayHelper::getColumn($modelsProgress, 'studyplan_subject_id');

        $modelsMarks = ArrayHelper::index(LessonItemsProgressView::find()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['studyplan_id' => $studyplan_id])
            ->andWhere(['=', 'test_category', 1])
            ->all(), null, ['studyplan_subject_id']); // Только по studyplan_subject_id, чтобы не потерять оценки других групп при переводе

        $modelsMarksCertif = ArrayHelper::index(AttestationItems::find()
            ->joinWith('lessonMark')
            ->select('attestation_items.id, attestation_items.plan_year, attestation_items.lesson_mark_id, attestation_items.studyplan_subject_id, guide_lesson_mark.mark_label as mark_label')
            ->where(['plan_year' => $plan_year])
            ->andWhere(['studyplan_subject_id' => $studyplanSubjectIds])
//            ->asArray()
            ->all(), null, ['studyplan_subject_id']);
       // echo '<pre>' . print_r($modelsMarksCertif, true) . '</pre>'; die();
        $attributes = ['studyplan_subject_id' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['subject_vid_id' => Yii::t('art/guide', 'Subject Vid')];
        $attributes += ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['pa' => 'ПА'];

        $dates = $columns = [];
        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d');
            $my = ArtHelper::getMonthsNominativeList()[date('n', $lessonDate['lesson_date'])] . ' ' . date('Y', $lessonDate['lesson_date']);
            $columns[$my] = isset($columns[$my]) ? $columns[$my] + 1 : 1;
            $attributes += [$date => $label];
            $dates[] = $date;
        }


        $data = [];
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['lesson_timestamp'] = $lessonDates;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['plan_year'] = $modelProgress->plan_year;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['sect_name'] = $modelProgress->sect_name;
            $data[$item]['subject'] = $modelProgress->subject;

            if (isset($modelsMarks[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarks[$modelProgress->studyplan_subject_id] as $id => $mark) {
//                    echo '<pre>' . print_r($mark, true) . '</pre>';
                    $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                    if (\artsoft\Art::isFrontend()) {
                        $helpIcon = \artsoft\helpers\Html::beginTag('span', [
                            'title' => $mark->lesson_topic ? $mark->lesson_topic . ' ' . $mark->lesson_rem : "Задание не определено.",
                            'data-content' => $mark->lesson_topic. ' ' . $mark->lesson_rem,
                            'data-html' => 'true',
                            'role' => 'button',
                            'style' => 'margin-bottom: 5px; padding: 0 5px;',
                            'class' => 'btn btn-sm role-help-btn',
                        ]);
                        $helpIcon .= $mark->mark_label . ($mark->mark_label ? '<span style="font-size: 6pt;">' . $mark->test_name_short . '</span>' : '');
                        $helpIcon .= \artsoft\helpers\Html::endTag('span');
                        $data[$item][$date_label] = $helpIcon;
                    } else {
                        $data[$item][$date_label] = !$readonly ? self::getEditableForm($mark_list, $mark) . ($mark->mark_label ? '<span style="font-size: 6pt;">' . $mark->test_name_short . '</span>' : '') : $mark->mark_label . ($mark->mark_label ? '<span style="font-size: 6pt;">' . $mark->test_name_short . '</span>' : '');
                    }
                }
            }
            if (isset($modelsMarksCertif[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarksCertif[$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $data[$item]['pa'] = !$readonly ? self::getEditableFormAttestation($mark_list_sertif, $mark) : ($mark->lessonMark->mark_label ?? '');
                }
            }

        }
//        echo '<pre>' . print_r($data, true) . '</pre>'; die();
        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'columns' => $columns];
    }

    public static function getDataTeachers($model_date, $teachers_id, $plan_year, $editTable = true, $history = false)
    {
        $mark_list = LessonMark::getMarkLabelForStudent([LessonMark::PRESENCE, LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);
        $mark_list_sertif = LessonMark::getMarkLabelForStudent([LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);

        $data = $dates = $modelsProgress = [];
        $columns = [];
        $timestamp = ArtHelper::getMonYearParamsFromArray([$model_date->date_in, $model_date->date_out]);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];

        $attributes = ['studyplan_subject_id' => 'Название предмета'];
        $attributes += ['subject_sect_studyplan_id' => 'Название группы'];
        $attributes += ['student_id' => 'Ученик'];
        $attributes += ['pa' => 'ПА'];
        $dates_load_total = 0;

        if ($model_date->subject_sect_studyplan_id != 0) {
            $lessonDates = LessonItemsProgressView::find()->select('lesson_date, test_name_short')->distinct()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
                ->andWhere(['=', 'subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id])
                ->orderBy('lesson_date')
                ->asArray()->all();

            $modelsProgress = self::find()
                ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
                ->andWhere(['=', 'subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id]);
            if (!$history) {
                $modelsProgress = $modelsProgress->andWhere(['OR',
                    ['status' => Studyplan::STATUS_ACTIVE],
                    ['AND',
                        ['status' => Studyplan::STATUS_INACTIVE],
                        ['status_reason' => [1, 2, 4]]
                    ]
                ]);
            }
            $modelsProgress = $modelsProgress->andWhere(['plan_year' => $plan_year])
                ->all();
            $studyplanSubjectIds = ArrayHelper::getColumn($modelsProgress, 'studyplan_subject_id');

            $modelsMarks = ArrayHelper::index(LessonItemsProgressView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['subject_sect_studyplan_id' => $model_date->subject_sect_studyplan_id])
                ->andWhere(['=', 'test_category', 1])
                ->all(), null, ['studyplan_subject_id']);

            $modelsMarksCertif = ArrayHelper::index(AttestationItems::find()
                ->joinWith('lessonMark')
                ->select('attestation_items.id, attestation_items.plan_year, attestation_items.lesson_mark_id, attestation_items.studyplan_subject_id, guide_lesson_mark.mark_label')
                ->where(['plan_year' => $plan_year])
                ->andWhere(['studyplan_subject_id' => $studyplanSubjectIds])
                ->all(), null, ['studyplan_subject_id']);

            $dates_load = 0;
            foreach ($lessonDates as $id => $lessonDate) {
                $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
                $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d') . ' ' . $lessonDate['test_name_short'];
                $my = ArtHelper::getMonthsNominativeList()[date('n', $lessonDate['lesson_date'])] . ' ' . date('Y', $lessonDate['lesson_date']);
                $columns[$my] = isset($columns[$my]) ? $columns[$my] + 1 : 1;
                $attributes += [$date => $label];
                if (Art::isBackend()) {
                    $datesArray = (new Query())->from('activities_schedule_view')
                        ->innerJoin('lesson_items', 'lesson_items.subject_sect_studyplan_id = activities_schedule_view.subject_sect_studyplan_id AND lesson_items.studyplan_subject_id = activities_schedule_view.studyplan_subject_id')
                        ->innerJoin('lesson_progress', 'lesson_progress.lesson_items_id = lesson_items.id')
                        ->select(new \yii\db\Expression('DISTINCT activities_schedule_view.subject_schedule_id,datetime_in,datetime_out,lesson_date,lesson_test_id,lesson_mark_id'))
                        ->where(['=', 'activities_schedule_view.subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id])
                        ->andWhere(['and', ['>=', 'datetime_in', $lessonDate['lesson_date']], ['<', 'datetime_in', $lessonDate['lesson_date'] + 86400]])
                        ->andWhere(['=', 'lesson_date', $lessonDate['lesson_date']])
                        ->andWhere(['=', 'direction_id', 1000])
                        ->andWhere(['IS NOT', 'lesson_mark_id', NULL])
                        ->one();
//                print_r($datesArray); die();
                    $dates_load = Schedule::astr2academ($datesArray['datetime_out'] - $datesArray['datetime_in']);
                    $dates_load_total += $dates_load;
                }
                $dates[] = ['date' => $date, 'dates_load' => $dates_load];
            }
        }
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['lesson_timestamp'] = $lessonDates;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['plan_year'] = $modelProgress->plan_year;
            $data[$item]['teachers_id'] = $teachers_id;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['student_id'] = $modelProgress->student_id;
            $data[$item]['sect_name'] = $modelProgress->sect_name;
            $data[$item]['student_fio'] = $modelProgress->student_fullname;
            $data[$item]['subject'] = $modelProgress->subject;
//            $data[$item]['timestamp_in'] = $timestamp_in;

            if (isset($modelsMarks[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarks[$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
                    $data[$item][$date_label] = $editTable ? self::getEditableForm($mark_list, $mark, $teachers_id) : $mark->mark_label;
                }
            }

            if (isset($modelsMarksCertif[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarksCertif[$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $data[$item]['pa'] = $editTable ? self::getEditableFormAttestation($mark_list_sertif, $mark) : $mark->mark_label;
                }
            }
        }

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'dates_load_total' => $dates_load_total, 'columns' => $columns];
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

    public static function getDataIndivTeachers($model_date, $teachers_id, $plan_year, $editTable = true, $history = false)
    {
        $mark_list = LessonMark::getMarkLabelForStudent([LessonMark::PRESENCE, LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);
        $mark_list_sertif = LessonMark::getMarkLabelForStudent([LessonMark::MARK, LessonMark::OFFSET_NONOFFSET, LessonMark::REASON_ABSENCE]);

        $data = $dates = $dates_load = $certif = [];
        $columns = [];
        $timestamp = ArtHelper::getMonYearParamsFromArray([$model_date->date_in, $model_date->date_out]);
        $timestamp_in = $timestamp[0];
        $timestamp_out = $timestamp[1];

        $attributes = ['studyplan_subject_id' => 'Название предмета'];
        $attributes += ['subject_sect_studyplan_id' => 'Название группы'];
        $attributes += ['student_id' => 'Ученик'];

                $modelsProgress = self::find()
            ->where(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->andWhere(['=', 'subject_key', $model_date->subject_key]);

        if (!$history) {
            $modelsProgress = $modelsProgress->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ]);
        }
        $modelsProgress = $modelsProgress->andWhere(['plan_year' => $plan_year])
            ->all();
       // echo '<pre>' . print_r($modelsProgress, true) . '</pre>'; die();
        $studyplanSubjectIds = ArrayHelper::getColumn($modelsProgress, 'studyplan_subject_id');
        $dates_load_total = 0;

        $modelsMarks = \Yii::$app->db->createCommand('SELECT * FROM "lesson_items_progress_view"  
	JOIN teachers_load ON teachers_load.subject_sect_studyplan_id = lesson_items_progress_view.subject_sect_studyplan_id 
	    AND teachers_load.studyplan_subject_id = lesson_items_progress_view.studyplan_subject_id
	JOIN subject_schedule ON subject_schedule.teachers_load_id = teachers_load.id 
	    AND subject_schedule.week_day = DATE_PART(\'dow\', to_timestamp(lesson_items_progress_view.lesson_date+10800))
	WHERE ("lesson_date" BETWEEN :timestamp_in AND :timestamp_out)  
	AND ("subject_key" = :subject_key) AND ("test_category" = 1) AND teachers_load.teachers_id = :teachers_id
	ORDER BY "lesson_date"',
            [
                'timestamp_in' => $timestamp_in,
                'timestamp_out' => $timestamp_out,
                'subject_key' => $model_date->subject_key,
                'teachers_id' => $teachers_id
            ])->queryAll();
        $lessonDates = array_values(array_unique(ArrayHelper::getColumn($modelsMarks, 'lesson_date')));
        $modelsMarksLoad = ArrayHelper::index($modelsMarks, null,['lesson_date']);
        //echo '<pre>' . print_r($modelsMarksLoad, true) . '</pre>'; die();
        foreach ($lessonDates as $id => $lessonDate) {
            $dates_load = 0;
            $date = Yii::$app->formatter->asDate($lessonDate, 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate, 'php:d');
            $my = ArtHelper::getMonthsNominativeList()[date('n', $lessonDate)] . ' ' . date('Y', $lessonDate);
            $columns[$my] = isset($columns[$my]) ? $columns[$my] + 1 : 1;
            $attributes += [$date => $label];
            if (Art::isBackend()) {
                if(isset($modelsMarksLoad[$lessonDate])) {
                    foreach ($modelsMarksLoad[$lessonDate] as $index => $m) {
                        $dates_load += $m['lesson_mark_id'] ? Schedule::astr2academ($m['time_out'] - $m['time_in']) : 0;
                    }
                    $dates_load_total += $dates_load;
                }
            }
            $dates[] = ['date' => $date, 'dates_load' => $dates_load];
        }
        $attributes += ['pa' => 'ПА'];

        $modelsMarks = ArrayHelper::index($modelsMarks, null, ['subject_sect_studyplan_id', 'studyplan_subject_id']);

        $modelsMarksCertif = ArrayHelper::index(AttestationItems::find()
            ->joinWith('lessonMark')
            ->select('attestation_items.id, attestation_items.plan_year, attestation_items.lesson_mark_id, attestation_items.studyplan_subject_id, guide_lesson_mark.mark_label')
            ->where(['plan_year' => $plan_year])
            ->andWhere(['studyplan_subject_id' => $studyplanSubjectIds])
            ->all(), null, ['studyplan_subject_id']);

        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['lesson_timestamp'] = $lessonDates;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['plan_year'] = $plan_year;
            $data[$item]['teachers_id'] = $teachers_id;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['student_id'] = $modelProgress->student_id;
            $data[$item]['sect_name'] = $modelProgress->sect_name;
            $data[$item]['student_fio'] = $modelProgress->student_fullname;
            $data[$item]['subject_key'] = $modelProgress->subject_key;
            $data[$item]['subject'] = $modelProgress->subject;
            $data[$item]['timestamp_in'] = $timestamp_in;

            if (isset($modelsMarks[$modelProgress->subject_sect_studyplan_id][$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarks[$modelProgress->subject_sect_studyplan_id][$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $date_label = Yii::$app->formatter->asDate($mark['lesson_date'], 'php:d.m.Y');
                    $data[$item][$date_label] = $editTable ? self::getEditableForm($mark_list, $mark, $teachers_id) . ($mark['mark_label'] ? '<span style="font-size: 6pt;">' . $mark['test_name_short'] . '</span>' : '') : ($mark['mark_label'] ?? '');
                }
            }

            if (isset($modelsMarksCertif[$modelProgress->studyplan_subject_id])) {
                foreach ($modelsMarksCertif[$modelProgress->studyplan_subject_id] as $id => $mark) {
                    $data[$item]['pa'] = $editTable ? self::getEditableFormAttestation($mark_list_sertif, $mark, $teachers_id) : ($mark->mark_label ?? '');
                }
            }
        }
        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'dates_load_total' => $dates_load_total, 'columns' => $columns];
    }

    public static function getIndivListForTeachersQuery($teachers_id, $plan_year, $history = false)
    {
        $models = self::find()
            ->select('subject_key, subject')
            ->distinct()
            ->where(['is not', 'subject_key', NULL]);
        if (!$history) {
            $models = $models->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ]);
        }
        $models = $models->andWhere(['plan_year' => $plan_year])
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->orderBy('subject');
        return $models;

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
    public static function getEditableForm($mark_list, $mark, $teachers_id = null)
    {
        return Editable::widget([
            'buttonsTemplate' => "{reset}{submit}",
            'name' => 'lesson_mark_id',
            'asPopover' => true,
            'disabled' => $teachers_id !== null ? (\artsoft\Art::isFrontend() && !Teachers::isOwnTeacher($teachers_id)) : false,
            'value' => $mark['lesson_mark_id'],
            'header' => Yii::$app->formatter->asDate($mark['lesson_date'], 'php:d.m.Y') . ' - ' . $mark['test_name'] . ($mark['lesson_topic'] ? ' (' . $mark['lesson_topic'] . ')' : ''),
            'displayValueConfig' => $mark_list,
            'format' => Editable::FORMAT_LINK,
            'inputType' => Editable::INPUT_DROPDOWN_LIST,
            'data' => $mark_list,
            'size' => 'md',
            'options' => ['class' => 'form-control', 'placeholder' => Yii::t('art', 'Select...')],
            'formOptions' => [
                'action' => Url::toRoute(['/studyplan/lesson-progress/set-mark', 'lesson_progress_id' => $mark['lesson_progress_id']]),
            ],
        ]);
    }

    public static function getEditableFormAttestation($mark_list, $mark, $teachers_id = null)
    {
        return Editable::widget([
            'buttonsTemplate' => "{reset}{submit}",
            'name' => 'lesson_mark_id',
            'asPopover' => true,
            'disabled' => $teachers_id !== null ? (\artsoft\Art::isFrontend() && !Teachers::isOwnTeacher($teachers_id)) : false,
            'value' => $mark->lesson_mark_id,
            'header' => 'ПА - ' . $mark->plan_year . '/' . ($mark->plan_year + 1) . ' учебный год.',
            'displayValueConfig' => $mark_list,
            'format' => Editable::FORMAT_LINK,
            'inputType' => Editable::INPUT_DROPDOWN_LIST,
            'data' => $mark_list,
            'size' => 'md',
            'options' => ['class' => 'form-control', 'placeholder' => Yii::t('art', 'Select...')],
            'formOptions' => [
                'action' => Url::toRoute(['/studyplan/lesson-progress/set-mark-attestation', 'attestation_id' => $mark->id]),
            ],
        ]);
    }
}
