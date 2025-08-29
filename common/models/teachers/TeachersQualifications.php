<?php

namespace common\models\teachers;

use artsoft\behaviors\DateFieldBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "teachers_qualifications".
 *
 * @property int $id
 * @property int $teachers_id
 * @property string $name
 * @property string $place
 * @property string $description
 * @property int|null $date
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 * @property int $status
 *
 * @property Teachers $teachers
 */
class TeachersQualifications extends \artsoft\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_qualifications';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['date'],
                'timeFormat' => 'd.m.Y',
            ],
            [
                'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teachers_id', 'name', 'place', 'status'], 'required'],
            [['status', 'version'], 'integer'],
            [['date'], 'safe'],
            [['version'], 'default', 'value' => 0],
            [['name'], 'string', 'max' => 254],
            [['place'], 'string', 'max' => 512],
            [['description'], 'string', 'max' => 1024],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'teachers_id' => Yii::t('art/teachers', 'Teacher'),
            'name' => Yii::t('art/guide', 'Name PPK'),
            'place' => Yii::t('art/guide', 'Place PPK'),
            'description' => Yii::t('art/guide', 'Description PPK'),
            'date' => Yii::t('art/guide', 'Date PPK'),
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


    public function getTeachersName()
    {
        return $this->teachers->getFullName();
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'teachers_id']);
    }

    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => 'ППК пройдена',
            self::STATUS_INACTIVE => 'ППК планируется',
        );
    }
    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->status == 0) {
            $this->date = null;
        }
        return parent::beforeSave($insert);
    }

}
