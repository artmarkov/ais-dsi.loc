<?php

namespace common\models\subjectsect;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\helpers\RefBook;
use common\models\schedule\SubjectScheduleView;
use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectType;
use common\models\subject\SubjectVid;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject_sect".
 *
 * @property int $id
 * @property string|null $programm_list Учебные рограммы
 * @property int $course_list Список курсов
 * @property int $term_mastering Период обучения
 * @property int $subject_cat_id
 * @property int $subject_id
 * @property int $subject_vid_id
 * @property int $subject_type_id
 * @property string|null $sect_name Название группы
 * @property int $course_flag Распределить по годам обучения(Да/Нет)
 * @property string|null $class_index Индекс курса
 * @property string|null $description Описание группы
 * @property int $sub_group_qty Кол-во подгрупп
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property GuideSubjectCategory $subjectCat
 * @property GuideSubjectType $subjectType
 * @property GuideSubjectVid $subjectVid
 * @property Subject $subject
 * @property SubjectSectStudyplan[] $subjectSectStudyplans
 */
class SubjectSect extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect';
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
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['programm_list', 'course_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_cat_id', 'subject_id', 'subject_vid_id', 'subject_type_id', 'course_flag', 'sub_group_qty'], 'required'],
            [['programm_list'], 'required'],
            [['term_mastering'], 'required', 'when' => function (SubjectSect $model) {
                return $model->course_flag == 1;
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"subjectsect-course_flag\"]').prop('checked');
                            }"],
            [['course_list', 'term_mastering', 'subject_cat_id', 'subject_id', 'subject_vid_id', 'subject_type_id', 'course_flag', 'sub_group_qty'], 'default', 'value' => null],
            [['term_mastering', 'subject_cat_id', 'subject_id', 'subject_vid_id', 'subject_type_id', 'course_flag', 'sub_group_qty', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'integer'],
            [['programm_list', 'course_list'], 'safe'],
            [['sect_name'], 'string', 'max' => 127],
            [['class_index'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 1024],
            [['subject_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::class, 'targetAttribute' => ['subject_cat_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::class, 'targetAttribute' => ['subject_type_id' => 'id']],
            [['subject_vid_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectVid::class, 'targetAttribute' => ['subject_vid_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'description' => Yii::t('art', 'Description'),
            'class_index' => Yii::t('art/guide', 'Class Index'),
            'term_mastering' => Yii::t('art/guide', 'Term Mastering'),
            'course_list' => 'Ограничения по классам',
            'programm_list' => Yii::t('art/guide', 'Programm List'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'sect_name' => Yii::t('art/guide', 'Sect Name'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'course_flag' => Yii::t('art/guide', 'Course Flag'),
            'sub_group_qty' => Yii::t('art/guide', 'Sub Group Qty'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'status' => Yii::t('art', 'Status'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[SubjectCat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCat()
    {
        return $this->hasOne(SubjectCategory::class, ['id' => 'subject_cat_id']);
    }

    /**
     * Gets query for [[SubjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::class, ['id' => 'subject_type_id']);
    }

    /**
     * Gets query for [[SubjectVid]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectVid()
    {
        return $this->hasOne(SubjectVid::class, ['id' => 'subject_vid_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }

    /**
     * Геттер названия группы
     * @return string
     */
    public function getClassIndex()
    {
        return $this->class_index;
    }

    /**
     * Gets query for [[SubjectSectStudyplans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSectStudyplans($plan_year)
    {
        return $this->hasMany(SubjectSectStudyplan::class, ['subject_sect_id' => 'id'])->where(['=', 'plan_year', $plan_year])->orderBy('group_num, course')->all();
    }

//    /**
//     * Список нагрузок преподавателей
//     * @return array
//     */
//    public function getSubjectSectTeachersLoad()
//    {
//        $data = [];
//        foreach ($this->subjectSectStudyplans as $index => $modelSubjectSectStudyplan) {
//            $sectClassName = $modelSubjectSectStudyplan->class_name;
//            $data[$sectClassName] = $modelSubjectSectStudyplan->getSubjectSectTeachersLoads();
//
//        }
//        return $data;
//    }

    /**
     * Полный список учеников подгрупп данной группы
     * @return string
     */
    public function getStudyplanList($plab_year)
    {
        $data = [];
        foreach ($this->getSubjectSectStudyplans($plab_year) as $item => $model) {
            $model['studyplan_subject_list'] != '' ? $data[] = $model['studyplan_subject_list'] : null;
        }
        return implode(',', $data);
    }

    /**
     * Запрос на получение претендентов на вступление в подгруппы по критериям
     * @return array
     * @throws \yii\db\Exception
     */
    public function getStudyplanForProgramms($plan_year, $course = null, $readonly = false)
    {
       // $this->subject_type_id = $this->subject_type_id == null ? 0 : $this->subject_type_id;
        $course = $course == null ? 0 : $course;
        $programm_list = implode(',', $this->programm_list);
        $funcSql = <<< SQL
            select *
            from studyplan_subject_view
            where education_programm_id = any (string_to_array('{$programm_list}', ',')::int[])
                and studyplan_subject_id != all(string_to_array('{$this->getStudyplanList($plan_year)}', ',')::int[])
                and plan_year = {$plan_year}
                and subject_category_id = {$this->subject_cat_id}
                and subject_id = {$this->subject_id}
                and subject_vid_id = {$this->subject_vid_id}
                and case when {$course} != 0 then course = {$course} else true end
		
SQL;
        $data = [];
        $nodels = Yii::$app->db->createCommand($funcSql)->queryAll();
        foreach ($nodels as $item => $nodel) {
            $data[$nodel['studyplan_subject_id']] = [
                'content' => SubjectSectStudyplan::getSubjectSectStudyplanContent($nodel),
                'disabled' => $readonly
            ];
        }
        return $data;
    }

    /**
     * @param $cat_id
     * @return string
     */
    protected static function getQuery($programm_list, $cat_id)
    {
        $programm_list = implode(',', $programm_list);
        return <<< SQL
            select distinct subject_id as id, subject.name as name
                from education_programm
                inner join education_programm_level on education_programm_level.programm_id = education_programm.id
                inner join education_programm_level_subject on education_programm_level_subject.programm_level_id = education_programm_level.id
                inner join guide_subject_category on guide_subject_category.id = education_programm_level_subject.subject_cat_id
                inner join subject on subject.id = education_programm_level_subject.subject_id
                inner join guide_subject_vid on guide_subject_vid.id = education_programm_level_subject.subject_vid_id
                where education_programm.id = any (string_to_array('{$programm_list}', ',')::int[])
                    and subject_id is not null
                    and education_programm_level_subject.subject_cat_id = {$cat_id}
SQL;
    }

    /**
     * @param $cat_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSubjectForUnionAndCat($programm_list, $cat_id)
    {
        return $cat_id ? ArrayHelper::map(Yii::$app->db->createCommand(self::getQuery($programm_list, $cat_id))->queryAll(), 'id', 'name') : [];
    }

    /**
     * @param $cat_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSubjectForUnionAndCatToId($programm_list, $cat_id)
    {
        return $cat_id ? Yii::$app->db->createCommand(self::getQuery($programm_list, $cat_id))->queryAll() : [];
    }

    public function getSubjectSchedule($model_date)
    {
        $models = SubjectScheduleView::find()
            ->where(['subject_sect_id' => $this->id])
            ->andWhere(['not', ['subject_schedule_id' => null]])
            ->andWhere(['=', 'plan_year', $model_date->plan_year])
            ->all();

        $data = [];

        foreach ($models as $item => $modelSchedule) {
            $data[] = [
                'week_day' => $modelSchedule->week_day,
                'time_in' => $modelSchedule->time_in,
                'time_out' => $modelSchedule->time_out,
                'title' => RefBook::find('sect_name_1')->getValue($modelSchedule->subject_sect_studyplan_id),
                'data' => [
                    'subject_sect_id' => $this->id,
                    'schedule_id' => $modelSchedule->subject_schedule_id,
                    'teachers_load_id' => $modelSchedule->teachers_load_id,
                    'direction_id' => $modelSchedule->direction_id,
                    'teachers_id' => $modelSchedule->teachers_id,
                    'description' => $modelSchedule->description,
                    'week_num' => $modelSchedule->week_num,
                    'week_day' => $modelSchedule->week_day,
                    'auditory_id' => $modelSchedule->auditory_id,
                    'style' => [
                        'background' => '#0000ff',
                        'color' => '#00ff00',
                        'border' => '#ff0000',
                    ]
                ]
            ];
        }
//        print_r($data);
        return $data;
    }

    /**
     * @param $model_date
     * @return array
     */
    public function setSubjectSect($model_date)
    {
        $modelsSubjectSectStudyplan = [];
        $sub_group_qty = $this->sub_group_qty;
        $course_list = $this->course_list;

        if ($this->course_flag) {
            for ($group = 1; $group <= $sub_group_qty; $group++) {
                foreach ($course_list as $item => $course) {
                    $m = SubjectSectStudyplan::find()->where(['=', 'subject_sect_id', $this->id])
                            ->andWhere(['=', 'group_num', $group])
                            ->andWhere(['=', 'plan_year', $model_date->plan_year])
                            ->andWhere(['=', 'course', $course])->one() ?? new SubjectSectStudyplan();
                    $m->subject_sect_id = $this->id;
                    $m->group_num = $group;
                    $m->plan_year = $model_date->plan_year;
                    $m->course = $course;
                    $m->subject_type_id = $this->subject_type_id;
                    $m->save(false);
                    $modelsSubjectSectStudyplan[] = $m;
                }
            }
        } else {
            for ($group = 1; $group <= $sub_group_qty; $group++) {
                $m = SubjectSectStudyplan::find()->where(['=', 'subject_sect_id', $this->id])
                        ->andWhere(['=', 'group_num', $group])
                        ->andWhere(['=', 'plan_year', $model_date->plan_year])->one() ?? new SubjectSectStudyplan();
                $m->subject_sect_id = $this->id;
                $m->group_num = $group;
                $m->plan_year = $model_date->plan_year;
                $m->subject_type_id = $this->subject_type_id;
                $m->save(false);
                $modelsSubjectSectStudyplan[] = $m;
            }
        }
        return $modelsSubjectSectStudyplan;
    }

    /**
     * @param $subject_sect_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getStudentsListForSect($subject_sect_id, $plan_year)
    {
        return ArrayHelper::map(Yii::$app->db->createCommand('SELECT studyplan_subject_id, student_fio 
                                                    FROM studyplan_subject_view 
                                                    WHERE subject_sect_id=:subject_sect_id AND plan_year=:plan_year ORDER BY student_fio',
            ['subject_sect_id' => $subject_sect_id,
                'plan_year' => $plan_year
            ])->queryAll(), 'studyplan_subject_id', 'student_fio');
    }

    /**
     * @param $subject_sect_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSectListForSect($subject_sect_id, $plan_year)
    {
        return ArrayHelper::map(Yii::$app->db->createCommand('SELECT id, sect_name_1
                                                    FROM subject_sect_view  
                                                    WHERE subject_sect_id=:subject_sect_id AND plan_year=:plan_year ORDER BY sect_name_1',
            ['subject_sect_id' => $subject_sect_id,
                'plan_year' => $plan_year
            ])->queryAll(), 'id', 'sect_name_1');
    }

    /**
     * @param $subject_sect_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getTeachersListForSect($subject_sect_id, $plan_year)
    {
        $q = Yii::$app->db->createCommand('SELECT distinct teachers_id
                                                    FROM teachers_load_view 
                                                    WHERE teachers_load_id IS NOT NULL AND subject_sect_id=:subject_sect_id AND plan_year=:plan_year',
            ['subject_sect_id' => $subject_sect_id,
                'plan_year' => $plan_year
            ])->queryColumn();
        $data = [];
        foreach ($q as $item => $value) {
            $data[$value] = RefBook::find('teachers_fio')->getValue($value);
        }

        return $data;

    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\db\StaleObjectException
     */
    public function beforeSave($insert)
    {
        if ($this->course_flag == 0) {
            $this->term_mastering = null;

            $models = SubjectSectStudyplan::find()->where(['=', 'subject_sect_id', $this->id])->andWhere(['not', ['course' => null]])->all();
            foreach ($models as $model) {
                $model->delete();
            }
        } else {
            $models = SubjectSectStudyplan::find()->where(['=', 'subject_sect_id', $this->id])->andWhere(['is', 'course', new \yii\db\Expression('null')])->all();
            foreach ($models as $model) {
                $model->delete();
            }
        }

        return parent::beforeSave($insert);
    }
}
