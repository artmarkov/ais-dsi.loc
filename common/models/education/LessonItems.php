<?php

namespace common\models\education;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\RefBook;
use Yii;
use artsoft\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "lesson_items".
 *
 * @property int $id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int $lesson_test_id
 * @property int $lesson_date
 * @property string|null $lesson_topic
 * @property string|null $lesson_rem
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property GuideLessonTest $lessonTest
 * @property LessonProgress[] $lessonProgresses
 */
class LessonItems extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_items';
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['lesson_date'],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'lesson_test_id', 'version'], 'integer'],
            [['lesson_test_id', 'lesson_date'], 'required'],
            [['lesson_date'], 'safe'],
            [['lesson_topic'], 'string', 'max' => 512],
            [['lesson_rem'], 'string', 'max' => 1024],
            [['lesson_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonTest::className(), 'targetAttribute' => ['lesson_test_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Subject Sect'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Studyplan Subject'),
            'lesson_test_id' => Yii::t('art/guide', 'Lesson Test'),
            'lesson_date' => Yii::t('art/guide', 'Lesson Date'),
            'lesson_topic' => Yii::t('art/guide', 'Lesson Topic'),
            'lesson_rem' => Yii::t('art/guide', 'Lesson Rem'),
            'created_at' => Yii::t('art/guide', 'Created'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }
    /**
     * Gets query for [[LessonTest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonTest()
    {
        return $this->hasOne(GuideLessonTest::className(), ['id' => 'lesson_test_id']);
    }

    /**
     * Gets query for [[LessonProgresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonProgresses()
    {
        return $this->hasMany(LessonProgress::className(), ['lesson_items_id' => 'id']);
    }
    /**
     * @param $model_date
     * @return array
     */
    public static function getData($model_date, $studyplan_id)
    {
        $timestamp_in = Yii::$app->formatter->asTimestamp($model_date->date_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($model_date->date_out) + 86399;

        $lessonDates = LessonItemsView::find()->select('lesson_date')->distinct()
            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'studyplan_id', $studyplan_id])
            ->orderBy('lesson_date')
            ->asArray()->all();

        $modelsProgress = LessonProgressView::find(['studyplan_id' => $studyplan_id])->all();

        $attributes = ['studyplan_subject_id' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['subject_vid_id' => Yii::t('art/guide', 'Subject Vid')];
        $attributes += ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['lesson_qty' => Yii::t('art/studyplan', 'Lesson Qty')];
        $attributes += ['current_qty' => Yii::t('art/studyplan', 'Current Qty')];
        $attributes += ['absence_qty' => Yii::t('art/studyplan', 'Absence Qty')];
        $attributes += ['current_avg_mark' => Yii::t('art/studyplan', 'Current Avg Mark')];
        $attributes += ['middle_avg_mark' => Yii::t('art/studyplan', 'Middle Avg Mark')];
        $attributes += ['finish_avg_mark' => Yii::t('art/studyplan', 'Finish Avg Mark')];

        $dates = [];
        foreach ($lessonDates as $id => $lessonDate) {
            $date_label =  Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
            $attributes += [$date_label => $date_label];
            $dates[] = $date_label;
        }

        $data = [];
        foreach ($modelsProgress as $item => $modelProgress) {
            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
            $data[$item]['subject_vid_id'] = $modelProgress->subject_vid_id;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
            $data[$item]['lesson_qty'] = $modelProgress->lesson_qty;
            $data[$item]['current_qty'] = $modelProgress->current_qty;
            $data[$item]['absence_qty'] = $modelProgress->absence_qty;
            $data[$item]['current_avg_mark'] = $modelProgress->current_avg_mark;
            $data[$item]['middle_avg_mark'] = $modelProgress->middle_avg_mark;
            $data[$item]['finish_avg_mark'] = $modelProgress->finish_avg_mark;

            $marks = LessonItemsView::find()
                ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
                ->andWhere(['=','studyplan_subject_id', $modelProgress->studyplan_subject_id])
                ->asArray()->all();
//
            foreach ($marks as $id => $mark) {
                $date_label =  Yii::$app->formatter->asDate($mark['lesson_date'], 'php:d.m.Y');
                    $data[$item][$date_label] = $mark['mark_label'];

            }
        }
//        usort($data, function ($a, $b) {
//            return $b['total'] <=> $a['total'];
//        });
        //echo '<pre>' . print_r($data, true) . '</pre>'; die();
         return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'studyplan_id' => $studyplan_id ];
    }

}
