<?php

namespace common\models\education;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\Schedule;
use common\models\schedule\SubjectScheduleView;
use common\models\studyplan\Studyplan;
use common\models\subjectsect\SubjectSectStudyplan;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

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
    const SCENARIO_COMMON =  'common';

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
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'version'], 'default', 'value' => 0],
            [['lesson_test_id', 'lesson_date'], 'required'],
            [['lesson_date'], 'safe'],
            [['lesson_date'], 'checkLessonExist', 'skipOnEmpty' => false, 'on' => self::SCENARIO_COMMON],
            [['lesson_date'], 'checkLessonDate', 'skipOnEmpty' => false, 'on' => self::SCENARIO_COMMON],
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

    /**
     * Проверка на существование занятия
     * @param $subject_sect_studyplan_id
     * @param $studyplan_subject_id
     * @param $lesson_timestamp
     * @return bool
     */
    public static function isLessonExist($subject_sect_studyplan_id, $studyplan_subject_id, $lesson_timestamp)
    {
        return self::find()->where(
            ['AND',
                ['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id],
                ['=', 'studyplan_subject_id', $studyplan_subject_id],
                ['=', 'lesson_date', $lesson_timestamp],
            ])->scalar();
    }

    public function checkLessonDate($attribute, $params)
    {
        $checkLesson = SubjectScheduleView::find()->where(
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
        return $this->hasOne(LessonTest::class, ['id' => 'lesson_test_id']);
    }

    /**
     * Gets query for [[LessonProgresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonProgresses()
    {
        return $this->hasMany(LessonProgress::class, ['lesson_items_id' => 'id']);
    }

    /**
     * Инициация списка оценок для группы или оценки для предмета ученика
     * @return array
     * @throws NotFoundHttpException
     */
    public function getLessonProgressNew(){
        if (!$this->subject_sect_studyplan_id && !$this->studyplan_subject_id) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр studyplan_subject_id или subject_sect_studyplan_id.");
        }
        $modelsItems = [];
        if($this->subject_sect_studyplan_id != 0) {
            $studyplanSubjectList = LessonProgressView::find()->select('studyplan_subject_id')
                ->andWhere(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
                ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
                ->column();
            foreach ($studyplanSubjectList as $item => $studyplan_subject_id) {
                $m = new LessonProgress();
                $m->studyplan_subject_id = $studyplan_subject_id;
                $modelsItems[] = $m;
            }
        } else {
            $m = new LessonProgress();
            $m->studyplan_subject_id = $this->studyplan_subject_id;
            $modelsItems[] = $m;
        }
        return $modelsItems;
    }

    /**
     * Формирует список оценок для группы или оценку для предмета ученика
     * @return array|LessonProgress[]|\yii\db\ActiveRecord[]
     */
    public function getLessonProgress()
    {
        $modelsItems = [];
        if ($this->subject_sect_studyplan_id != 0) {
            $studyplan = [];
            $models = LessonItemsProgressView::find()
                ->where(['=', 'lesson_items_id', $this->id])
                ->all();

            foreach ($models as $item => $modelItems) {
                $m = LessonProgress::find()->where(['=', 'id', $modelItems->lesson_progress_id])->one();
                if($m) {
                    $studyplan[] = $modelItems->studyplan_subject_id;
                    $modelsItems[] = $m;
                }
            }
            $models = LessonProgressView::find()->select('studyplan_subject_id')
                ->andWhere(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
                ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
                ->column();
            foreach ($models as $item => $studyplan_subject_id) {
                if (in_array($studyplan_subject_id, $studyplan)) {
                    continue;
                }
                $m = new LessonProgress();
                $m->studyplan_subject_id = $studyplan_subject_id;
                $m->lesson_items_id = $this->id;
                $m->save(false);
                $modelsItems[] = $m;
            }
        } else {
            $modelsItems = $this->lessonProgresses;
        }
        return $modelsItems;
    }
//    public function getLessonProgress()
//    {
//        if ($this->subject_sect_studyplan_id != 0) {
//            // находим только оценки тех учеников, которые числяться в данной группе(переведенные игнорируются)
//            $modelsItems = LessonProgress::find()
//                ->innerJoin('lesson_items', 'lesson_items.id = lesson_progress.lesson_items_id and lesson_items.studyplan_subject_id = 0')
//                ->innerJoin('subject_sect_studyplan', 'subject_sect_studyplan.id = lesson_items.subject_sect_studyplan_id')
//                ->where(['=', 'lesson_items.id', $this->id])
//                ->andWhere(new \yii\db\Expression("lesson_progress.studyplan_subject_id = any (string_to_array(subject_sect_studyplan.studyplan_subject_list, ',')::int[])"))
//                ->all();
//            // список учеников с оценками
//            $list = LessonProgress::find()->select('studyplan_subject_id')->where(['=', 'lesson_items_id', $this->id])->distinct()->column();
//            // список всех учеников группы
//            $studyplanSubjectList = SubjectSectStudyplan::findOne($this->subject_sect_studyplan_id)->studyplan_subject_list;
//            // добавляем новые модели вновь принятых учеников
//            $list_new = array_unique(explode(',', $studyplanSubjectList));
//
//            foreach (array_diff($list_new, $list) as $item => $studyplan_subject_id) {
//                $m = new LessonProgress();
//                $m->studyplan_subject_id = $studyplan_subject_id;
//                $modelsItems[] = $m;
//            }
//        } else {
//            $modelsItems = $this->lessonProgresses;
//        }
//        return $modelsItems;
//    }

    /**
     * Инициация оценок для инд. занятий
     * @param $teachers_id
     * @param $subject_key
     * @param $timestamp_in
     * @param $model
     * @return array
     * @throws NotFoundHttpException
     */
    public function getLessonProgressTeachersNew($teachers_id, $subject_key, $timestamp_in, $model)
    {
        $modelsItems = [];
        if (!$subject_key && !$timestamp_in && !$teachers_id) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр teachers_id или subject_key или timestamp_in.");
        }
        $modelsProgress = LessonProgressView::find()
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->andWhere(['=', 'subject_key', $subject_key])
            ->andWhere(['=', 'plan_year', ArtHelper::getStudyYearDefault(null, $timestamp_in)])
            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
            ->all();
        foreach ($modelsProgress as $item => $modelProgress) {
            if (!self::checkLessonSchedule($modelProgress, $model->lesson_date)) {
                continue;
            }
            $m = new LessonProgress();
            $m->studyplan_subject_id = $modelProgress->studyplan_subject_id;
            $modelsItems[] = $m;

        }
//        echo '<pre>' . print_r($modelsItems, true) . '</pre>';
        return $modelsItems;
    }

    /**
     * Формирует список оценок для инд. занятий
     * @param $teachers_id
     * @param $subject_key
     * @param $timestamp_in
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function getLessonProgressTeachers($teachers_id, $subject_key, $timestamp_in)
    {
        $modelsItems = [];
        if (!$subject_key && !$timestamp_in && !$teachers_id) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр teachers_id или subject_key или timestamp_in.");
        }
        $modelsProgress = LessonProgressView::find()
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->andWhere(['=', 'subject_key', $subject_key])
            ->andWhere(['=', 'plan_year', ArtHelper::getStudyYearDefault(null, $timestamp_in)])
            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
            ->all();
        foreach ($modelsProgress as $item => $model) {
            if (!self::checkLessonSchedule($model, \Yii::$app->formatter->asDate($timestamp_in, 'php:d.m.Y'))) {
                continue;
            }
            $modelProgress = LessonItemsProgressView::find()
                ->where(
                    ['AND',
                        ['=', 'subject_sect_studyplan_id', $model->subject_sect_studyplan_id],
                        ['=', 'studyplan_subject_id', $model->studyplan_subject_id],
                        ['=', 'lesson_date', $timestamp_in],
                    ])
                ->one();
            $m = LessonProgress::findOne(['id' => $modelProgress['lesson_progress_id']]) ?? new LessonProgress();
            $m->studyplan_subject_id = $model->studyplan_subject_id;
            $m->lesson_items_id = $modelProgress['lesson_items_id'];
            $m->save(false);
            $modelsItems[] = $m;
        }
        return $modelsItems;
    }

    /**
     * Проверка, соответствует ли расписание выбранной дате
     * @param $model
     * @param $lesson_date
     * @return bool
     */
    public static function checkLessonSchedule($model, $lesson_date)
    {
        return SubjectScheduleView::find()->where(
            ['AND',
                ['=', 'subject_sect_studyplan_id', $model->subject_sect_studyplan_id],
                ['=', 'studyplan_subject_id', $model->studyplan_subject_id],
                ['=', 'week_day', Schedule::timestamp2WeekDay(strtotime($lesson_date))],

            ])->exists();
    }

    /**
     * Проверка на существование урока для индивидуальных занятий
     * @param $model
     * @param $lesson_date
     * @return bool
     */
    public static function checkLessonIndiv($model, $lesson_date)
    {
        return  LessonItemsProgressView::find()
            ->where(['=', 'subject_sect_studyplan_id', 0])
            ->andWhere(['=', 'studyplan_subject_id', $model->studyplan_subject_id])
            ->andWhere(['=', 'lesson_date', strtotime($lesson_date)])
            ->exists();
    }

    /**
     * Проверка всех уроков индивидуальных занятий и удаление существующих уроков
     * Остаются незаполненные для добавления оценок новым ученикам
     *
     * @param $modelsProgress
     * @param $lesson_date
     * @return mixed
     */
    public static function checkLessonsIndiv($modelsProgress, $lesson_date)
    {
        foreach ($modelsProgress as $item => $modelProgress) {
            if (self::checkLessonIndiv($modelProgress, $lesson_date)) {
                unset($modelsProgress[$item]);
            }
        }
        return $modelsProgress;
    }

    public function beforeDelete()
    {
        $this->updated_at = time();
        $this->updated_by = Yii::$app->user->identity->getId();
        $this->save();
        return parent::beforeDelete();
    }
}
