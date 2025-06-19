<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\models\User;
use common\models\own\Department;
use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectVid;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "education_programm".
 *
 * @property int $id
 * @property int $education_cat_id
 * @property string|null $name
 * @property string|null $short_name
 * @property string|null $term_mastering
 * @property string|null $description
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property EducationCat $educationCat
 */
class EducationProgramm extends \artsoft\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_programm';
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
            [['name', 'short_name', 'term_mastering', 'education_cat_id', 'status'], 'required'],
            [['education_cat_id', 'status', 'version'], 'integer'],
            [['status'], 'default', 'value' => null],
            [['name', 'short_name', 'term_mastering'], 'string', 'max' => 512],
            [['description'], 'string', 'max' => 1024],
            [['education_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationCat::class, 'targetAttribute' => ['education_cat_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'education_cat_id' => Yii::t('art/guide', 'Education Cat'),
            'name' => Yii::t('art', 'Name'),
            'short_name' => Yii::t('art', 'Short Name'),
            'term_mastering' => Yii::t('art/guide', 'Term Mastering'),
            'description' => Yii::t('art', 'Description'),
            'status' => Yii::t('art', 'Status'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }


    /**
     * Gets query for [[EducationCat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationCat()
    {
        return $this->hasOne(EducationCat::class, ['id' => 'education_cat_id']);
    }

    /**
     * @return string
     */
    public function getCatName()
    {
        return $this->educationCat->name;
    }

//    public function getCatType()
//    {
//        return $this->educationCat->type_id;
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgrammLevel()
    {
        return $this->hasMany(EducationProgrammLevel::class, ['programm_id' => 'id'])->orderBy('course');
    }

    /**
     * @param $category_id
     * @return array|Subject[]|\common\models\subject\SubjectQuery
     */
    public function getSubjectById($category_id)
    {
        $data = [];
        if ($category_id) {
            $data = Subject::find()->select(['id', 'name']);
            $data = $data->andFilterWhere(['like', 'category_list', $category_id]);
            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
            $data = $data->asArray()->all();
        }
        return $data;
    }

    /**
     * Получаем возможные дисциплины программы выбранной категории
     * @param $category_id
     * @return array|\common\models\subject\SubjectQuery
     */
    public function getSubjectByCategory($category_id)
    {
        $data = [];
        if ($category_id) {
            $data = Subject::find()->select(['name', 'id']);
            $data = $data->andFilterWhere(['like', 'category_list', $category_id]);
            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
            $data = $data->indexBy('id')->column();
        }
        return $data;
    }

    public function copy()
    {
        $modelsEducationProgrammLevel = $this->programmLevel;
        $index = count($modelsEducationProgrammLevel);
        if ($index < $this->term_mastering) {
            if (!empty($modelsEducationProgrammLevel[$index - 1])) {
                $modelEducationProgrammLevel = $modelsEducationProgrammLevel[$index - 1];
                $m = new EducationProgrammLevel();
                $modelEducationProgrammLevel->programm_id = $this->id;
                $modelEducationProgrammLevel->course = $index + 1;
                $modelEducationProgrammLevel->level_id = null;
                $m->setAttributes($modelEducationProgrammLevel->getAttributes());
                $m->save(false);
                $modelsEducationProgrammLevelSubject = $modelEducationProgrammLevel->educationProgrammLevelSubject;
                foreach ($modelsEducationProgrammLevelSubject as $index2 => $modelEducationProgrammLevelSubject) {
                    $mm = new EducationProgrammLevelSubject();
                    $modelEducationProgrammLevelSubject->programm_level_id = $m->id;
                    $mm->setAttributes($modelEducationProgrammLevelSubject->getAttributes());
                    $mm->save(false);
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getProgrammList()
    {
        return ArrayHelper::map(self::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->select('id, short_name as name')
            ->orderBy('short_name')
            ->asArray()->all(), 'id', 'name');
    }

    public static function getProgrammListByName($cat_id = false)
    {
        if(!$cat_id) return [];

        return ArrayHelper::map(self::find()
            ->select('id, short_name as name')
            ->filterWhere(['=', 'education_cat_id', $cat_id])
            ->andFilterWhere(['=', 'status', self::STATUS_ACTIVE])
            ->orderBy('short_name')
            ->asArray()->all(), 'id', 'name');
    }

    public static function getProgrammListById($cat_id)
    {
        $data = [];
        if ($cat_id) {
            $data = self::find()->select('id, short_name as name');
            $data = $data->filterWhere(['=', 'education_cat_id', $cat_id]);
            $data = $data->andFilterWhere(['=', 'status', self::STATUS_ACTIVE]);
            $data = $data->asArray()->all();
        }
        return $data;
    }
/*
    public function getSubjectListByProgramm($category_id = 1000)
    {
        $data = (new Query())->from('education_programm_level')
            ->innerJoin('education_programm_level_subject', 'education_programm_level_subject.programm_level_id = education_programm_level.id')
            ->innerJoin('subject', 'subject.id = education_programm_level_subject.subject_id')
            ->select('subject.id, subject.name')
            ->where(['=', 'education_programm_level.programm_id', $this->id])
            ->andWhere(['=', 'education_programm_level_subject.subject_cat_id', $category_id])
            ->all();
        echo '<pre>' . print_r($data, true) . '</pre>';
    }*/

    /**
     * @return false|int|null|string
     */
    public static function getProgrammScalar()
    {
        return self::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->select('id')
            ->scalar();
    }

    public function beforeDelete()
    {
        $model = CostEducation::findOne(['programm_id' => $this->id]);
        if (!$model->delete()) {
            return false;
        }
        return parent::beforeDelete();
    }
}
