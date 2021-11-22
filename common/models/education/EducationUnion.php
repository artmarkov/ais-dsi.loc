<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use common\models\studygroups\SubjectSect;
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

//    public function getSubjectById($category_id)
//    {
//        $data = [];
//        if ($category_id) {
//            $data = Subject::find()->select(['id', 'name']);
//            foreach ($this->getSpecialityDepartments($speciality_id) as $item => $department_id) {
//                $data->orWhere(['like', 'department_list', $department_id]);
//
//            }
//            $data = $data->andFilterWhere(['like', 'category_list', $category_id]);
//            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
//            $data = $data->asArray()->all();
//        }
//        return $data;
//    }
//
//    /**
//     * Получаем возможные дисциплины программы выбранной категории
//
//     */
//    public function getSubjectByCategory($category_id)
//    {
//        $data = [];
//        if ($category_id) {
//            $data = Subject::find()->select(['name', 'id']);
//            foreach ($this->getSpecialityDepartments($speciality_id) as $item => $department_id) {
//                $data->orWhere(['like', 'programm_list', $department_id]);
//
//            }
//            $data = $data->andFilterWhere(['like', 'programm_list', $category_id]);
//            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
//            $data = $data->indexBy('id')->column();
//        }
//        return $data;
//    }


    public function getSubjectByProgramList()
    {
        $data = [];
    foreach ($this->programm_list as $item => $programm_id) {
        $data += EducationProgrammLevel::findOne(['programm_id' => $programm_id])->educationProgrammLevelSubject;
//        foreach ($data as $speciality => $programm_id) {
//            $dep = self::getSpecialityDepartments();
//        }
    }
        echo '<pre>' . print_r($data) . '</pre>';
        //sort($data);
        return $data;
    }

}
