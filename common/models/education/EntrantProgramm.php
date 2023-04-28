<?php

namespace common\models\education;

use artsoft\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "entrant_programm".
 *
 * @property int $id
 * @property int $programm_id Учебная программа
 * @property int $subject_type_id
 * @property int $course
 * @property string $name Название программы для предварительной записи
 * @property int $age_in Ограничение по возрасту снизу
 * @property int $age_out Ограничение по возрасту сверху
 * @property int $qty_entrant Кол-во учеников для приема
 * @property int $qty_reserve Кол-во учеников для резерва
 * @property string|null $description
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 *
 * @property EntrantPreregistrations[] $entrantPreregistrations
 * @property EducationProgramm $programm
 */
class EntrantProgramm extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_programm';
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
            [['programm_id', 'subject_type_id', 'course', 'name', 'age_in', 'age_out', 'qty_entrant', 'qty_reserve'], 'required'],
            [['programm_id', 'subject_type_id', 'course', 'age_in', 'age_out', 'qty_entrant', 'qty_reserve', 'status'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['programm_id' => 'id']],
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
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'course' => Yii::t('art/guide', 'Course'),
            'name' => Yii::t('art/guide', 'Name'),
            'age_in' => Yii::t('art/student', 'Age In'),
            'age_out' => Yii::t('art/student', 'Age Out'),
            'qty_entrant' => Yii::t('art/student', 'Qty Entrant'),
            'qty_reserve' => Yii::t('art/student', 'Qty Reserve'),
            'description' => Yii::t('art', 'Description'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
        ];
    }

    /**
     * Gets query for [[EntrantPreregistrations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantPreregistrations()
    {
        return $this->hasMany(EntrantPreregistrations::class, ['entrant_programm_id' => 'id']);
    }

    /**
     * Gets query for [[Programm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramm()
    {
        return $this->hasOne(EducationProgramm::class, ['id' => 'programm_id']);
    }
}
