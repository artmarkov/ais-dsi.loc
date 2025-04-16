<?php

namespace common\models\subjectsect;

use artsoft\helpers\RefBook;
use common\models\education\LessonProgress;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subject\SubjectType;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * This is the model class for table "subject_sect_studyplan".
 *
 * @property int $id
 * @property int|null $subject_sect_id
 * @property int|null $plan_year
 * @property int|null $course
 * @property string|null $studyplan_subject_list
 * @property int $group_num
 * @property int $subject_type_id
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property GuideSubjectType $subjectType
 * @property SubjectSect $subjectSect
 */
class SubjectSectStudyplan extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect_studyplan';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_num', 'subject_type_id', 'subject_sect_id', 'plan_year'], 'required'],
            [['studyplan_subject_list'], 'string'],
            [['subject_sect_id', 'plan_year', 'course', 'subject_type_id', 'group_num'], 'integer'],
            [['studyplan_subject_list'], 'string'],
            ['group_num', 'unique', 'targetAttribute' => ['group_num', 'subject_sect_id', 'plan_year', 'course'], 'message' => 'Номер группы не должен повторяться.'],
            [['subject_sect_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectSect::class, 'targetAttribute' => ['subject_sect_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::class, 'targetAttribute' => ['subject_type_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect'),
            'plan_year' => Yii::t('art/guide', 'Plan_year'),
            'course' => Yii::t('art/guide', 'Course'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'group_num' => Yii::t('art/guide', 'Group Num'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
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
     * Gets query for [[SubjectSect]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSect()
    {
        return $this->hasOne(SubjectSect::class, ['id' => 'subject_sect_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersLoads()
    {
        return $this->hasMany(TeachersLoad::class, ['subject_sect_studyplan_id' => 'id']);
    }

    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::class, ['id' => 'subject_type_id']);
    }

    /**
     * @return array
     */
//    public function getSubjectSectTeachersLoads()
//    {
//        $data = [];
//        foreach ($this->teachersLoads as $item => $modelTeachersLoad) {
//            $data[$modelTeachersLoad->id] = RefBook::find('teachers_load_display')->getValue($modelTeachersLoad->id);
//        }
//        return $data;
//    }

    /**
     * @return array
     */
    public function getSubjectSectStudyplans($readonly = false)
    {
        $data = [];
        if (!empty($this->studyplan_subject_list)) {
            $modelsItems = (new Query())->from('studyplan_subject_view')
                ->where(new \yii\db\Expression("studyplan_subject_id = any (string_to_array('{$this->studyplan_subject_list}', ',')::int[])"))
                ->andWhere(['OR',
                    ['status' => Studyplan::STATUS_ACTIVE],
                    ['AND',
                        ['status' => Studyplan::STATUS_INACTIVE],
                        ['status_reason' => [1, 2, 3, 4]]
                    ]
                ])
                ->orderBy('student_fio')
                ->all();
           // print_r($this->studyplan_subject_list); die();
            foreach ($modelsItems as $item => $model) {
                $data[$model['studyplan_subject_id']] = [
                    'content' => $this->getSubjectSectStudyplanContent($model),
                    'disabled' => $readonly
                ];
            }
        }
        return $data;
    }

    /**
     * @param $studyplan_subject_id
     * @return string
     */
    public static function getSubjectSectStudyplanContent($model)
    {
        //$student_id = RefBook::find('studyplan_subject-student')->getValue($studyplan_subject_id);
//        return '<div style="overflow: hidden;">
//                <div class="pull-left">' . RefBook::find('students_fio')->getValue($student_id) . '</div>' .
//               '<div class="fa-pull-right">' . RefBook::find('subject_memo_1')->getValue($studyplan_subject_id) . '</div></div>';
     return '<div style="overflow: hidden;">
                <div class="pull-left">' . $model['student_fullname'] . '</div>' .
               '<div class="fa-pull-right">' . $model['memo_2'] . ' ' . $model['education_programm_short_name'] . ' ' . $model['course'] . ' кл. ' . $model['speciality'] . '</div></div>';
    }

    /**
     * удаляет злемент из studyplan_subject_list
     * @param $studyplan_subject_id
     * @return $this
     */
    public function removeStudyplanSubject($studyplan_subject_id)
    {
        $list = [];
        $this->studyplan_subject_list != '' ? $list = explode(',', $this->studyplan_subject_list) : null;
        if (($key = array_search($studyplan_subject_id, $list)) !== false) {
            unset($list[$key]);
            $this->studyplan_subject_list = implode(',', $list);
            $this->save(false);
        }
        return $this;
    }

    /**
     * добавляет злемент в studyplan_subject_list
     * @param $studyplan_subject_id
     * @return $this
     */
    public function insertStudyplanSubject($studyplan_subject_id)
    {
        $list = [];
        $this->studyplan_subject_list != '' ? $list = explode(',', $this->studyplan_subject_list) : null;
        array_push($list, $studyplan_subject_id);
        $this->studyplan_subject_list = implode(',', $list);
        $this->save(false);
        return $this;
    }

    /**
     * Удаление задвоений в группе и сортировка по алфавиту
     */
    public function normaliseStudyplanSubject()
    {
        $studyplanSubjectIds = (new \yii\db\Query())->select(['studyplan_subject_id', 'student_fio'])
            ->from('studyplan_subject_view')
            ->distinct()
            ->where(new \yii\db\Expression("studyplan_subject_id = any (string_to_array('{$this->studyplan_subject_list}', ',')::int[])"))
            ->orderBy('student_fio')
            ->column();
        $this->studyplan_subject_list = implode(',', $studyplanSubjectIds);
    }

    public function beforeSave($insert)
    {
        $this->normaliseStudyplanSubject();
        return parent::beforeSave($insert);
    }

//    public function beforeDelete()
//    {
//        $ids = $this->getTeachersLoads()->column();
//        if ($ids) {
//            TeachersLoad::deleteAll(['id' => $ids]);
//        }
//        return parent::beforeDelete();
//    }
}
