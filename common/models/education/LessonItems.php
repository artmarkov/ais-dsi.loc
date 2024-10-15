<?php

namespace common\models\education;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use artsoft\widgets\Notice;
use common\models\schedule\SubjectScheduleView;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subjectsect\SubjectSectStudyplan;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
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
 * @property LessonTest $lessonTest
 * @property LessonProgress[] $lessonProgresses
 */
class LessonItems extends \artsoft\db\ActiveRecord
{
    const SCENARIO_COMMON = 'common';

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
            [['lesson_test_id'], 'checkLessonTestExist', 'skipOnEmpty' => false, 'on' => self::SCENARIO_COMMON],
            [['lesson_date'], 'checkLessonExist', 'skipOnEmpty' => false, 'on' => self::SCENARIO_COMMON],
            [['lesson_date'], 'checkLessonDate', 'skipOnEmpty' => false, 'on' => self::SCENARIO_COMMON],
            [['lesson_test_id'], 'checkLessonTest', 'skipOnEmpty' => false, 'on' => self::SCENARIO_COMMON],
            [['lesson_topic'], 'string', 'max' => 512],
            [['lesson_rem'], 'string', 'max' => 1024],
            [['lesson_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonTest::className(), 'targetAttribute' => ['lesson_test_id' => 'id']],
        ];
    }

    public function checkLessonTestExist($attribute, $params)
    {
        if ($this->isNewRecord) {
            $test = LessonTest::findOne($this->lesson_test_id);
            if ($test->test_category != 1) {
                $checkLesson = self::find()->where(
                    ['AND',
                        ['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id],
                        ['=', 'studyplan_subject_id', $this->studyplan_subject_id],
                        ['=', 'lesson_test_id', $this->lesson_test_id],

                    ]);
                if ($checkLesson->exists() === true) {
                    $this->addError($attribute, 'Данный вид занятия уже существует для дисциплины!');
                }
            }
        }
    }

    public function checkLessonTest($attribute, $params)
    {
        $res = $studentsFio = [];
        $test = LessonTest::findOne($this->lesson_test_id);
        if ($test->test_category != 1) {
            $studyplanSubjectIds = array_column(array_filter($_POST['LessonProgress'], function ($item) {
                return $item['lesson_mark_id'] != null;
            }), 'studyplan_subject_id');
            foreach ($studyplanSubjectIds as $studyplan_subject_id) {
                if ($test->test_category == 2) {
                    $checkLesson = LessonProgress::find()
                        ->joinWith('studyplanSubject')
                        ->where(['studyplan_subject.med_cert' => true])
                        ->andWhere(['lesson_progress.studyplan_subject_id' => $studyplan_subject_id]);
                } elseif ($test->test_category == 3) {
                    $checkLesson = LessonProgress::find()
                        ->joinWith('studyplanSubject')
                        ->where(['studyplan_subject.fin_cert' => true])
                        ->andWhere(['lesson_progress.studyplan_subject_id' => $studyplan_subject_id]);
                }
                if ($checkLesson->exists() === false) {
                    $res[] = $studyplan_subject_id;
                }
            }
            if (!empty($res)) {
                    $studentsFio = (new \yii\db\Query())->select('student_fio')->from('studyplan_subject_view')->distinct()
                        ->where(['studyplan_subject_id' => $res])
                        ->orderBy('student_fio')
                        ->column();
                $message = 'Данный вид занятия - "' . $test->test_name . '" недоступен в учебном плане ученика: ' . implode(',', $studentsFio);
                // Notice::registerDanger($message);
                $this->addError($attribute, $message);
            }
        }
    }

    public function checkLessonExist($attribute, $params)
    {
        if ($this->isNewRecord) {
            $test = LessonTest::findOne($this->lesson_test_id);
            if ($test->test_category == 1) {
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
        return self::find()->joinWith('lessonTest')->where(
            ['AND',
                ['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id],
                ['=', 'studyplan_subject_id', $studyplan_subject_id],
                ['=', 'lesson_date', $lesson_timestamp],
                ['=', 'test_category', 1],
            ])->scalar();
    }

    public static function isLessonCertifExist($subject_sect_studyplan_id, $studyplan_subject_id, $lesson_test_id)
    {
        return self::find()->joinWith('lessonTest')->where(
            ['AND',
                ['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id],
                ['=', 'studyplan_subject_id', $studyplan_subject_id],
                ['=', 'lesson_test_id', $lesson_test_id],
                ['!=', 'test_category', 1],
            ])->scalar();
    }

    public function checkLessonDate($attribute, $params)
    {
        $test = LessonTest::findOne($this->lesson_test_id);
        if ($test->test_category == 1) {
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
    public function getLessonProgressNew()
    {
        if (!$this->subject_sect_studyplan_id && !$this->studyplan_subject_id) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр studyplan_subject_id или subject_sect_studyplan_id.");
        }
        $modelsItems = [];
        $i = 0;
        if ($this->subject_sect_studyplan_id != 0) {
            $studyplanSubjectList = LessonProgressView::find()->select('studyplan_subject_id')
                ->andWhere(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
                ->andWhere(['OR',
                    ['status' => Studyplan::STATUS_ACTIVE],
                    ['AND',
                        ['status' => Studyplan::STATUS_INACTIVE],
                        ['status_reason' => [1, 2, 4]]
                    ]
                ])
                ->column();
            foreach ($studyplanSubjectList as $item => $studyplan_subject_id) {
                $m = new LessonProgress();
                $m->studyplan_subject_id = $studyplan_subject_id;
                $modelsItems[$i] = $m;
                $i++;
            }
        } else {
            $m = new LessonProgress();
            $m->studyplan_subject_id = $this->studyplan_subject_id;
            $modelsItems[1] = $m;
        }
        return $modelsItems;
    }

    /**
     * Формирует список оценок для группы или оценку для предмета ученика
     * @return array|LessonProgress[]|\yii\db\ActiveRecord[]
     */
    public function getLessonProgress()
    {
        print_r($this->subject_sect_studyplan_id);
        $modelsItems = [];
        $i = 0;
        if ($this->subject_sect_studyplan_id != 0) {
            $studyplan = [];
            $models1 = LessonItemsProgressView::find()
                ->where(['=', 'lesson_items_id', $this->id])
                ->andWhere(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
               // ->andWhere(['status' => Studyplan::STATUS_ACTIVE])
                ->andWhere(['OR',
                    ['status' => Studyplan::STATUS_ACTIVE],
                    ['AND',
                        ['status' => Studyplan::STATUS_INACTIVE],
                        ['status_reason' => [1, 2, 4]]
                    ]
                ])
                ->all();

            foreach ($models1 as $item => $modelItems) {
                $m = LessonProgress::find()->where(['=', 'id', $modelItems->lesson_progress_id])->one();
                if ($m) {
                    $studyplan[] = $modelItems->studyplan_subject_id;
                    $modelsItems[$i] = $m;
                    $i++;
                }
            }
            $models2 = LessonProgressView::find()->select('studyplan_subject_id')
                ->where(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
               // ->andWhere(['status' => Studyplan::STATUS_ACTIVE])
                ->andWhere(['OR',
                    ['status' => Studyplan::STATUS_ACTIVE],
                    ['AND',
                        ['status' => Studyplan::STATUS_INACTIVE],
                        ['status_reason' => [1, 2, 4]]
                    ]
                ])
                ->column();
            foreach ($models2 as $item => $studyplan_subject_id) {
                if (!in_array($studyplan_subject_id, $studyplan)) {
                $m_new = new LessonProgress();
                    $m_new->studyplan_subject_id = $studyplan_subject_id;
                    $m_new->lesson_items_id = $this->id;
                    $m_new->save(false);
                $modelsItems[$i] = $m_new;
                $i++;
                }
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
        $test_category = LessonTest::getLessonTestCategory($model->lesson_test_id);
        $modelsProgress = LessonProgressView::find()
            ->andWhere(new \yii\db\Expression(":teachers_id = any (string_to_array(teachers_list, ',')::int[])", [':teachers_id' => $teachers_id]))
            ->andWhere(['=', 'subject_key', $subject_key])
            ->andWhere(['=', 'plan_year', ArtHelper::getStudyYearDefault(null, $timestamp_in)])
            ->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ])
            ->all();
        foreach ($modelsProgress as $item => $modelProgress) {
            if ($test_category == LessonTest::CURRENT_WORK && !self::checkLessonSchedule($modelProgress, $model->lesson_date)) {
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
            ->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ])
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
    public static function checkLessonIndiv($modelProgress, $model)
    {
        return LessonItemsProgressView::find()
            ->where(['=', 'subject_sect_studyplan_id', 0])
            ->andWhere(['=', 'studyplan_subject_id', $modelProgress->studyplan_subject_id])
            ->andWhere(['=', 'lesson_date', strtotime($model->lesson_date)])
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
    public static function checkLessonsIndiv($modelsProgress, $model)
    {
        foreach ($modelsProgress as $item => $modelProgress) {
            if (self::checkLessonIndiv($modelProgress, $model)) {
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
