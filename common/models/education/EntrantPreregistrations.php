<?php

namespace common\models\education;

use artsoft\models\User;
use common\models\students\Student;
use Yii;

/**
 * This is the model class for table "entrant_preregistrations".
 *
 * @property int $id
 * @property int $entrant_programm_id Выбранная программа для предварительной записи
 * @property int $stydent_id Учетная запись ученика-кандидата
 * @property int $plan_year Учебный год приема ученика
 * @property int $reg_vid Вид записи (Список: для приема, в резерв)
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 *
 * @property EntrantProgramm $entrantProgramm
 * @property Students $stydent
 */
class EntrantPreregistrations extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_preregistrations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plan_year', 'entrant_programm_id', 'stydent_id', 'reg_vid'], 'required'],
            [['plan_year', 'entrant_programm_id', 'stydent_id', 'reg_vid', 'status'], 'integer'],
            [['entrant_programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntrantProgramm::class, 'targetAttribute' => ['entrant_programm_id' => 'id']],
            [['stydent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['stydent_id' => 'id']],
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
            'entrant_programm_id' => Yii::t('art/guide', 'Entrant Programm ID'),
            'stydent_id' => Yii::t('art/guide', 'Stydent ID'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'reg_vid' => Yii::t('art/guide', 'Reg Vid'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
        ];
    }

    /**
     * Gets query for [[EntrantProgramm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantProgramm()
    {
        return $this->hasOne(EntrantProgramm::class, ['id' => 'entrant_programm_id']);
    }

    /**
     * Gets query for [[Stydent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStydent()
    {
        return $this->hasOne(Student::class, ['id' => 'stydent_id']);
    }

}
