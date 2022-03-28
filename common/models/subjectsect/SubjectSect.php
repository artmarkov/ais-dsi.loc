<?php

namespace common\models\subjectsect;

use artsoft\helpers\RefBook;
use \common\models\education\EducationUnion;
use common\models\schedule\SubjectScheduleView;
use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectType;
use common\models\subject\SubjectVid;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject_sect".
 *
 * @property int $id
 * @property int|null $plan_year
 * @property int $union_id
 * @property int|null $course
 * @property int $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property SubjectSchedule[] $sectSchedules
 * @property EducationUnion $educationUnion
 * @property GuideSubjectCategory $subjectCat
 * @property GuideSubjectType $subjectType
 * @property GuideSubjectVid $subjectVid
 * @property Subject $subject
 * @property TeachersLoad[] $teachersLoads
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plan_year', 'union_id', 'subject_cat_id', 'subject_id', 'subject_vid_id'], 'required'],
            [['plan_year', 'union_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id'], 'integer'],
            [['union_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationUnion::class, 'targetAttribute' => ['union_id' => 'id']],
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
            'union_id' => Yii::t('art/guide', 'Education Union'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
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
     * Gets query for [[Programm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnion()
    {
        return $this->hasOne(EducationUnion::class, ['id' => 'union_id']);
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
        return isset($this->union) ? $this->union->class_index : 'Класс';
    }

    /**
     * Gets query for [[SubjectSectStudyplans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSectStudyplans()
    {
        return $this->hasMany(SubjectSectStudyplan::class, ['subject_sect_id' => 'id'])->orderBy('class_name');
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
    public function getStudyplanList()
    {
        $data = [];
        foreach ($this->getSubjectSectStudyplans()->asArray()->all() as $item => $model) {
            $model['studyplan_subject_list'] != '' ? $data[] = $model['studyplan_subject_list'] : null;
        }
        return implode(',', $data);
    }

    /**
     * Запрос на получение претендентов на вступление в подгруппы по критериям
     * @return array
     * @throws \yii\db\Exception
     */
    public function getStudyplanForUnion($readonly)
    {
//        $subQuery = EducationUnion::find()
//            ->select('programm_list')
//            ->where(['=', 'id', $this->union_id])
//            ->scalar();
//      $query =  Studyplan::find()
//            ->innerJoin('studyplan_subject', 'studyplan.id = studyplan_subject.studyplan_id')
//            ->select('studyplan_subject.id')
//            ->where(new \yii\db\Expression( "studyplan.programm_id = any (string_to_array(($subQuery), ',')::int[])"))
//            ->all();

        $this->subject_type_id = $this->subject_type_id == null ? 0 : $this->subject_type_id;
        $this->course = $this->course == null ? 0 : $this->course;

        $funcSql = <<< SQL
    select studyplan_subject.id as id
	from studyplan
	inner join studyplan_subject on studyplan.id = studyplan_subject.studyplan_id
	where studyplan.programm_id = any (string_to_array((
        select programm_list from education_union where id = {$this->union_id}), ',')::int[])
		and studyplan_subject.id != all(string_to_array('{$this->getStudyplanList()}', ',')::int[])
		and plan_year = {$this->plan_year}
        and subject_cat_id = {$this->subject_cat_id}
        and subject_id = {$this->subject_id}
        and subject_vid_id = {$this->subject_vid_id}
        and case when {$this->subject_type_id} != 0 then subject_type_id = {$this->subject_type_id} else subject_type_id is not null end
        and case when {$this->course} != 0 then course = {$this->course} else course is not null end
		
SQL;
        $data = [];
        $query = Yii::$app->db->createCommand($funcSql)->queryAll();
        foreach ($query as $item => $value) {
            $data[$value['id']] = [
                'content' => SubjectSectStudyplan::getSubjectSectStudyplanContent($value['id']),
                'disabled' => $readonly
            ];
        }
        return $data;
    }

    /**
     * список категорий дисциплин заданной группы планов (кроме индивидуальных qty_max > 1)
     * @param $union_id
     * @return array
     * @throws \yii\db\Exception
     */
    protected static function getQuerySub($union_id)
    {
        return <<< SQL
    select distinct subject_cat_id as id, guide_subject_category.name as name
	from studyplan
	inner join studyplan_subject on studyplan.id = studyplan_subject.studyplan_id
	inner join guide_subject_category on guide_subject_category.id = studyplan_subject.subject_cat_id
	inner join subject on subject.id = studyplan_subject.subject_id
	inner join guide_subject_vid on guide_subject_vid.id = studyplan_subject.subject_vid_id
	where studyplan.programm_id = any (string_to_array((
        select programm_list from education_union where id = {$union_id}), ',')::int[])
        and subject_id is not null
        and guide_subject_vid.qty_max > 1
SQL;
    }

    /**
     * @param $union_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSubjectCategoryForUnion($union_id)
    {
        return $union_id ? ArrayHelper::map(Yii::$app->db->createCommand(self::getQuerySub($union_id))->queryAll(), 'id', 'name') : [];
    }

    /**
     * @param $union_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSubjectCategoryForUnionToId($union_id)
    {
        return $union_id ? Yii::$app->db->createCommand(self::getQuerySub($union_id))->queryAll() : [];
    }

    /**
     * @param $cat_id
     * @return string
     */
    protected static function getQuery($union_id, $cat_id)
    {
        return <<< SQL
    select distinct subject_id as id, subject.name as name
	from studyplan
	inner join studyplan_subject on studyplan.id = studyplan_subject.studyplan_id
	inner join guide_subject_category on guide_subject_category.id = studyplan_subject.subject_cat_id
	inner join subject on subject.id = studyplan_subject.subject_id
	inner join guide_subject_vid on guide_subject_vid.id = studyplan_subject.subject_vid_id
	where studyplan.programm_id = any (string_to_array((
        select programm_list from education_union where id = {$union_id}), ',')::int[])
        and subject_id is not null
        and guide_subject_vid.qty_max > 1
        and studyplan_subject.subject_cat_id = {$cat_id}
SQL;
    }

    /**
     * @param $cat_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSubjectForUnionAndCat($union_id, $cat_id)
    {
        return $cat_id ? ArrayHelper::map(Yii::$app->db->createCommand(self::getQuery($union_id, $cat_id))->queryAll(), 'id', 'name') : [];
    }

    /**
     * @param $cat_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSubjectForUnionAndCatToId($union_id, $cat_id)
    {
        return $cat_id ? Yii::$app->db->createCommand(self::getQuery($union_id, $cat_id))->queryAll() : [];
    }

    public function getSubjectSchedule()
    {
        $models = SubjectScheduleView::find()
            ->where(['subject_sect_id' => $this->id])
            ->andWhere(['not', ['subject_schedule_id' => null]])
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
}
