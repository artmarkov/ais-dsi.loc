<?php

namespace common\models\studyplan;

use common\models\education\EducationProgramm;
use common\models\studygroups\SubjectSect;
use common\models\studygroups\SubjectSectStudyplan;
use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectType;
use common\models\subject\SubjectVid;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "studyplan_subject".
 *
 * @property int $id
 * @property int $studyplan_id
 * @property int $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property float|null $week_time
 * @property float|null $year_time
 * @property float|null $cost_hour
 * @property float|null $cost_month_summ
 * @property float|null $cost_year_summ
 * @property float|null $year_time_consult
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property EducationProgramm $studyplan
 * @property SubjectCategory $subjectCat
 * @property SubjectType $subjectType
 * @property Subject $subject
 */
class StudyplanSubject extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_subject';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'week_time', 'year_time'], 'required'],
            [['studyplan_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'status', 'version'], 'integer'],
            [['week_time', 'year_time', 'cost_hour', 'cost_month_summ', 'cost_year_summ', 'year_time_consult'], 'number'],
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
            'id' => Yii::t('art/studyplan', 'ID'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'year_time' => Yii::t('art/guide', 'Year Time'),
            'cost_hour' => Yii::t('art/guide', 'Cost Week Hour'),
            'cost_month_summ' => Yii::t('art/guide', 'Month Summ'),
            'cost_year_summ' => Yii::t('art/guide', 'Year Summ'),
            'year_time_consult' => Yii::t('art/guide', 'Year Time Consult'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Studyplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplan()
    {
        return $this->hasOne(Studyplan::class, ['id' => 'studyplan_id']);
    }

    /**
     * геттер Курс
     *
     * @return mixed|null
     */
    public function getCourse()
    {
        return isset($this->studyplan) ? $this->studyplan->course : null;
    }

    /**
     * геттер Год обучения
     *
     * @return mixed|null
     */
    public function getPlanYear()
    {
        return isset($this->studyplan) ? $this->studyplan->plan_year : null;
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
     * Вид дисциплины: групповые/индивидуальные и пр.
     *
     * Gets query for [[SubjectVid]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectVid()
    {
        return $this->hasOne(SubjectVid::class, ['id' => 'subject_vid_id']);
    }

    /**
     * Геттер названия вида дисциплины
     * @return null|string
     */
    public function getSubjectVidName()
    {
        return isset($this->subjectVid) ? $this->subjectVid->name : null;
    }

    /**
     * проверка на негрупповое занятие
     *
     * @return \yii\db\ActiveQuery
     */
    public function isIndividual()
    {
        return $this->subjectVid->qty_max == 1 ? true : false;
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
     * Находим группу дисциплины ученика
     * @return int
     * @throws \yii\db\Exception
     */
    public function getSubjectSectStudyplan()
    {
        return SubjectSectStudyplan::find()->where(['like', 'studyplan_list', $this->id])->one() ?? new SubjectSectStudyplan();
    }

    /**
     * находим все возможные группы для выбранной дисциплины
     * @return array
     * @throws \yii\db\Exception
     */
    public function getSubjectSectStudyplanAll()
    {
        $funcSql = <<< SQL
    select subject_sect_studyplan.id as id,
           CONCAT(course, '/' ,education_union.class_index, '_',subject_sect_studyplan.class_name) as name
	from subject_sect_studyplan
	inner join subject_sect on subject_sect.id = subject_sect_studyplan.subject_sect_id
	inner join education_union on education_union.id = subject_sect.union_id
	where subject_id = {$this->subject_id}
		and subject_type_id = {$this->subject_type_id}
		and subject_vid_id = {$this->subject_vid_id}
		and subject_cat_id = {$this->subject_cat_id}
		and course = {$this->getCourse()}
		and plan_year = {$this->getPlanYear()}
		order by name
SQL;
        return ArrayHelper::map(Yii::$app->db->createCommand($funcSql)->queryAll(), 'id', 'name');
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
     * @param $model
     * @param $modelSubTime
     */
    public function copyAttributes($model, $modelSubTime)
    {
        $this->studyplan_id = $model->id;
        $this->subject_cat_id = $modelSubTime->subject_cat_id;
        $this->subject_id = $modelSubTime->subject_id;
        $this->subject_type_id = $model->getTypeScalar();
        $this->subject_vid_id = $modelSubTime->subject_vid_id;
        $this->week_time = $modelSubTime->week_time;
        $this->year_time = $modelSubTime->year_time;
        $this->cost_hour = $modelSubTime->cost_hour;
        $this->cost_month_summ = $modelSubTime->cost_month_summ;
        $this->cost_year_summ = $modelSubTime->cost_year_summ;
        $this->year_time_consult = $modelSubTime->year_time_consult;
    }
     * т.о. достигается независимость дисциплины от плана
     *
     * @return bool
     */
    public function setAtributesSubjectSect()
            $model->course = $this->getPlanYear();
            $model->subject_cat_id = $this->subject_cat_id;
            $model->subject_id = $this->subject_id;
            $model->subject_type_id = $this->subject_type_id;
            $model->subject_vid_id = $this->subject_vid_id;
            }
            return false;
}
