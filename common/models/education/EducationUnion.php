<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use common\models\studygroups\SubjectSect;
use common\models\studyplan\Studyplan;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "education_union".
 *
 * @property int $id
 * @property string|null $union_name
 * @property string|null $description
 * @property string $programm_list
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property SubjectSect[] $subjectSects
 */
class EducationUnion extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_union';
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
                'attributes' => ['programm_list'],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programm_list', 'union_name'], 'required'],
            [['programm_list'], 'safe'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'version', 'status'], 'integer'],
            [['description'], 'string', 'max' => 1024],
            [['union_name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'union_name' => Yii::t('art/guide', 'Union Name'),
            'description' => Yii::t('art', 'Description'),
            'programm_list' => Yii::t('art/guide', 'Programm List'),
            'status' => Yii::t('art', 'Status'),
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


    public function getSubjectSects()
    {
        return $this->hasMany(SubjectSect::class, ['union_id' => 'id']);
    }


    public function getSubjectByProgramList()
    {
        $data = [];
    foreach ($this->programm_list as $item => $programm_id) {
        $models = Studyplan::findAll(['programm_id' => $programm_id]);
                echo '<pre>' . print_r($models) . '</pre>';
//        if($model->getStudyplanSubject()) {
//            $studuPlan = $model->getStudyplanSubject()->asArray()->all();
//            foreach ($studuPlan as $i => $value) {
//                echo '<pre>' . print_r($value['subject_cat_id']) . '</pre>';
//                echo '<pre>' . print_r($value['subject_id']) . '</pre>';
//            }
//        }
//        foreach ($data as $speciality => $programm_id) {
//            $dep = self::getSpecialityDepartments();
//        }
    }
        /*SELECT distinct subject_cat_id, subject_id, guide_subject_category.name as category_name, guide_subject_category.slug as category_slug, subject.name as subject_name, subject.slug as subject_slug
	FROM studyplan
	inner join studyplan_subject on studyplan.id = studyplan_subject.studyplan_id
	inner join guide_subject_category on guide_subject_category.id = studyplan_subject.subject_cat_id
	inner join subject on subject.id = studyplan_subject.subject_id
	where studyplan.programm_id = any (string_to_array((
    select programm_list from education_union where id = 1000
								   ), ',')::int[]) and subject_id is not null;

    SELECT distinct subject_cat_id,  guide_subject_category.name as category_name
	FROM studyplan
	inner join studyplan_subject on studyplan.id = studyplan_subject.studyplan_id
	inner join guide_subject_category on guide_subject_category.id = studyplan_subject.subject_cat_id
	inner join subject on subject.id = studyplan_subject.subject_id
	where studyplan.programm_id = any (string_to_array((
    select programm_list from education_union where id = 1000
								   ), ',')::int[])
        and subject_id is not null;

    SELECT distinct subject_id, subject.name as subject_name
	FROM studyplan
	inner join studyplan_subject on studyplan.id = studyplan_subject.studyplan_id
	inner join guide_subject_category on guide_subject_category.id = studyplan_subject.subject_cat_id
	inner join subject on subject.id = studyplan_subject.subject_id
	where studyplan.programm_id = any (string_to_array((
    select programm_list from education_union where id = 1000
								   ), ',')::int[])
        and subject_id is not null
        and studyplan_subject.subject_cat_id = 1000;
        */
        //sort($data);
        return $data;
    }

}
