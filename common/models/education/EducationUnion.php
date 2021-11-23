<?php

namespace common\models\education;

use artsoft\Art;
use artsoft\behaviors\ArrayFieldBehavior;
use common\models\studygroups\SubjectSect;
use common\models\studyplan\Studyplan;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

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

}
