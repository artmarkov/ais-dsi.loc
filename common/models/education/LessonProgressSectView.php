<?php

namespace common\models\education;

use artsoft\helpers\Html;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "lesson_progress_sect_view".
 *
 * @property int|null $subject_sect_id
 * @property int|null $plan_year
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int|null $studyplan_id
 * @property int|null $student_id
 */
class LessonProgressSectView extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_progress_sect_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/student', 'Student'),
        ];
    }

    public static function getData($model_date, $subject_sect_id)
    {
        $timestamp_in = Yii::$app->formatter->asTimestamp($model_date->date_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($model_date->date_out) + 86399;

        $lessonDates = LessonItemsProgressSectView::find()->select('lesson_date')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'subject_sect_id', $subject_sect_id])
            ->orderBy('lesson_date')
            ->asArray()->all();

        $modelsProgress = LessonProgressSectView::findAll(['subject_sect_id' => $subject_sect_id]);
       // print_r($modelsProgress); die();
        $attributes = ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['student_id' => Yii::t('art/student', 'Student')];

        $dates = [];
        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.y');
            $attributes += [$date => $label];
            $dates[] = $date;
        }
        $data = [];
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['id'] = $item+1;
            $data[$item]['subject_sect_id'] = $modelProgress->subject_sect_id;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['student_id'] = $modelProgress->student_id;

            $marks = LessonItemsProgressSectView::find()
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
                $data[$item][$date_label] = Html::a(implode('/', $mark_label[$date_label]),
                    Url::to(['/sect/default/studyplan-progress', 'id' => $data[$item]['subject_sect_id'], 'objectId' => $mark->id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Update'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );

            }
        }
//        usort($data, function ($a, $b) {
//            return $b['total'] <=> $a['total'];
//        });

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'subject_sect_id' => $subject_sect_id];
    }
}
