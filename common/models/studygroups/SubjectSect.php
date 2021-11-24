<?php

namespace common\models\studygroups;

use artsoft\helpers\RefBook;
use common\models\activities\SectSchedule;
use \common\models\education\EducationUnion;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
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
 * @property SectSchedule[] $sectSchedules
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
            [['plan_year', 'union_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id'], 'default', 'value' => null],
            [['plan_year', 'union_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['union_id', 'subject_cat_id'], 'required'],
            [['studyplan_list'], 'safe'],
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
            'id' => Yii::t('art/guide', 'ID'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'union_id' => Yii::t('art/guide', 'Education Union'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'created_at' => Yii::t('art/guide', 'Created'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'version' => Yii::t('art/guide', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[SectSchedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSectSchedules()
    {
        return $this->hasMany(SectSchedule::class, ['sect_id' => 'id']);
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
        return $this->hasOne(GuideSubjectType::class, ['id' => 'subject_type_id']);
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
     * Gets query for [[TeachersLoads]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersLoads()
    {
        return $this->hasMany(TeachersLoad::class, ['sect_id' => 'id']);
    }

    /**
     * Gets query for [[SubjectSectStudyplans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSectStudyplans()
    {
        return $this->hasMany(SubjectSectStudyplan::className(), ['subject_sect_id' => 'id']);
    }

    /**
     * Полный список учеников подгрупп данной группы
     * @return string
     */
    public function getStudyplanList()
    {
        $data = [];
        foreach ($this->getSubjectSectStudyplans()->asArray()->all() as $item => $model) {
            $data[] = $model['studyplan_list'];
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
        $funcSql = <<< SQL
    select studyplan_subject.id as id, student_id
	from studyplan
	inner join studyplan_subject on studyplan.id = studyplan_subject.studyplan_id
	where studyplan.programm_id = any (string_to_array((
        select programm_list from education_union where id = {$this->union_id}), ',')::int[])
        and subject_id = {$this->subject_id}
		and subject_type_id = {$this->subject_type_id}
		and subject_vid_id = {$this->subject_vid_id}
		and subject_cat_id = {$this->subject_cat_id}
		and course = {$this->course}
		and plan_year = {$this->plan_year}
		and studyplan_subject.id != all(string_to_array('{$this->getStudyplanList()}', ',')::int[])
SQL;
        $data = [];
        foreach (Yii::$app->db->createCommand($funcSql)->queryAll() as $item => $value) {
            $data[$value['id']] = [
                'content' => RefBook::find('students_fio')->getValue($value['student_id']).RefBook::find('memo_1')->getValue($value['id']),
                'disabled'=> $readonly
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
}
