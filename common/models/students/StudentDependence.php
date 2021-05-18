<?php

namespace common\models\students;

use artsoft\models\User;
use common\models\guidesys\UserRelation;
use common\models\parents\Parents;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "student_dependence".
 *
 * @property int $id
 * @property int|null $relation_id
 * @property int|null $student_id
 * @property int|null $parent_id
 * @property int|null $signer_flag
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property UserRelation $relation0
 * @property User $student
 * @property User $parent
 */
class StudentDependence extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_dependence';
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
            [['relation_id', 'parent_id'], 'required'],
            [['relation_id', 'student_id', 'parent_id', 'signer_flag', 'version'], 'integer'],
            [['created_at', 'created_by', 'updated_at', 'updated_by',], 'safe'],
            [['relation_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRelation::className(), 'targetAttribute' => ['relation_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Parents::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/students', 'ID'),
            'relation_id' => Yii::t('art/students', 'Relation'),
            'student_id' => Yii::t('art/students', 'Student'),
            'parent_id' => Yii::t('art/students', 'Parent'),
            'signer_flag' => Yii::t('art/students', 'Signer'),
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
     * Gets query for [[Relation0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelation0()
    {
        return $this->hasOne(UserRelation::className(), ['id' => 'relation_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(User::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(User::className(), ['id' => 'parent_id']);
    }
}
