<?php

namespace common\models\subject;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\db\ActiveRecord;
use common\models\entrant\EntrantComm;
use common\models\own\Department;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $department_list
 * @property string $category_list
 * @property string $vid_list
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 * @property int $status
 *
 */
class Subject extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['department_list', 'category_list', 'vid_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['name', 'slug', 'department_list', 'category_list', 'vid_list'], 'required'],
            ['status', 'integer'],
            [['name'], 'string', 'max' => 64],
            [['slug'], 'string', 'max' => 32],
            [['department_list', 'category_list', 'vid_list'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name'),
            'slug' => Yii::t('art/guide', 'Slug'),
            'department_list' => Yii::t('art/guide', 'Department'),
            'category_list' => Yii::t('art/guide', 'Subject Category'),
            'vid_list' => Yii::t('art/guide', 'Subject Vid'),
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
     * {@inheritdoc}
     * @return SubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubjectQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getSubjectById($category_id = null) {
        $data = self::find()->select(['id','name']);
             $data = $category_id ? $data->where(['like', 'category_list', $category_id]) : $data;
        $data = $data->asArray()->all();

        return $data;
    }

    public static function getSubjectByCategory($category_id = null)
    {
        $data = self::find()->select(['name', 'id']);
        $data = $category_id ? $data->where(['like', 'category_list', $category_id]) : $data;
        $data = $data->indexBy('id')->column();

        return $data;
    }

    public static function getSubjectByCategoryForName($category_id = null)
    {
        $data = self::find()->select(['name', 'name']);
        $data = $category_id ? $data->where(['like', 'category_list', $category_id]) : $data;
        $data = $data->indexBy('name')->column();

        return $data;
    }

    /**
     * только групповые предметы С группировкой по виду
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSubjectListGroupForVid()
    {
        $funcSql = <<< SQL
           SELECT subject.id AS id,
                  subject.name AS name,
                  guide_subject_vid.name as vid_name
            FROM guide_subject_vid, subject 
            WHERE guide_subject_vid.id = ANY(string_to_array(subject.vid_list, ',')::int[]) 
            AND guide_subject_vid.qty_min <> 1 
            AND guide_subject_vid.qty_max <> 1
            ORDER BY guide_subject_vid.id, subject.name;
		
SQL;
        return ArrayHelper::map(Yii::$app->db->createCommand($funcSql)->queryAll(), 'id', 'name', 'vid_name');
    }

    /**
     * только групповые предметы Без группировки по виду
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getSubjectListGroup()
    {
        $funcSql = <<< SQL
           SELECT DISTINCT subject.id AS id,
                  subject.name AS name
            FROM guide_subject_vid, subject 
            WHERE guide_subject_vid.id = ANY(string_to_array(subject.vid_list, ',')::int[]) 
            AND guide_subject_vid.qty_min <> 1 
            AND guide_subject_vid.qty_max <> 1
            AND subject.status = 1
            ORDER BY subject.name;
		
SQL;
        return ArrayHelper::map(Yii::$app->db->createCommand($funcSql)->queryAll(), 'id', 'name');
    }

}
