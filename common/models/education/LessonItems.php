<?php

namespace common\models\education;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\Html;
use artsoft\helpers\Schedule;
use common\models\subjectsect\SubjectScheduleTeachersView;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

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
            [['lesson_date'], 'checkLessonExist', 'skipOnEmpty' => false],
            [['lesson_date'], 'checkLessonDate', 'skipOnEmpty' => false],
            [['lesson_topic'], 'string', 'max' => 512],
            [['lesson_rem'], 'string', 'max' => 1024],
            [['lesson_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonTest::className(), 'targetAttribute' => ['lesson_test_id' => 'id']],
        ];
    }

    public function checkLessonExist($attribute, $params)
    {
        if ($this->isNewRecord) {
            $checkLesson = self::find()->where(
                ['AND',
                    ['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id],
                    ['=', 'studyplan_subject_id', $this->studyplan_subject_id],
                    ['=', 'lesson_date', strtotime($this->lesson_date)],

                ]);
            if ($checkLesson->exists() === true) {
                $this->addError($attribute, 'Занятие уже добавлено для выбранной даты и дисциплины!');
            }
        }
    }

//    public function checkLesson()
//    {
//            $checkLesson = self::find()->where(
//                ['AND',
//                    ['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id],
//                    ['=', 'studyplan_subject_id', $this->studyplan_subject_id],
//                    ['=', 'lesson_date', strtotime($this->lesson_date)],
//
//                ]);
//           return $checkLesson->scalar();
//    }

    public function checkLessonDate($attribute, $params)
    {
            $checkLesson = SubjectScheduleTeachersView::find()->where(
                ['AND',
                    ['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id],
                    ['=', 'studyplan_subject_id', $this->studyplan_subject_id],
                    ['=', 'week_day', Schedule::timestamp2WeekDay(strtotime($this->lesson_date))],

                ]);
            if ($checkLesson->exists() !== true) {
                $this->addError($attribute, 'Дата занятия не соответствует расписанию!');
            }
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
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
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
        return $this->hasOne(LessonTest::className(), ['id' => 'lesson_test_id']);
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

        $modelsProgress = LessonProgressView::findAll(['studyplan_id' => $studyplan_id]);

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
            $data[$item]['subject_vid_id'] = $modelProgress->subject_vid_id;
            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;

            $marks = LessonItemsView::find()
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
                    Url::to(['/studyplan/default/studyplan-progress', 'id' => $data[$item]['studyplan_id'], 'objectId' => $mark->id, 'mode' => 'update']), [
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

        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes, 'studyplan_id' => $studyplan_id];
    }

}
