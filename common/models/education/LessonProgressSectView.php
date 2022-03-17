<?php

namespace common\models\education;

use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use Yii;
use yii\helpers\Url;
use yii\web\JsExpression;

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
        $data = $dates = [];

        $timestamp_in = Yii::$app->formatter->asTimestamp($model_date->date_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($model_date->date_out) + 86399;

        $attributes = ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['student_id' => Yii::t('art/student', 'Student')];

        $lessonDates = LessonItemsProgressSectView::find()->select('lesson_date, lesson_items_id')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'subject_sect_id', $subject_sect_id])
            ->orderBy('lesson_date')
            ->asArray()->all();
        $modelsProgress = LessonProgressSectView::findAll(['subject_sect_id' => $subject_sect_id]);

        foreach ($lessonDates as $id => $lessonDate) {
            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.y');
            $attributes += [$date => $label];
            $dates[] = $date;
        }
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['lesson_timestamp'] = $lessonDates;
            $data[$item]['subject_sect_id'] = $modelProgress->subject_sect_id;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
            $data[$item]['student_id'] = $modelProgress->student_id;

            $marks = LessonItemsProgressSectView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=', 'studyplan_subject_id', $modelProgress->studyplan_subject_id])
                ->all();

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
                        'action' => Url::toRoute(['/sect/default/set-mark', 'lesson_progress_id' => $mark->lesson_progress_id]),
                    ],
                ]);
            }
        }

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'subject_sect_id' => $subject_sect_id];
    }
}
