<?php

namespace common\models\education;

use Yii;

/**
 * This is the model class for table "guide_cost_education".
 *
 * @property int $id
 * @property int|null $programm_id Программа обучения
 * @property float $standard_basic Норматив базовый, руб.
 * @property float $standard_basic_ratio Коэффициент к базовому нормативу
 *
 * @property EducationProgramm $programm
 */
class CostEducation extends \artsoft\db\ActiveRecord
{
    public $standard;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_cost_education';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programm_id'], 'integer'],
            [['programm_id'], 'unique', 'message' => 'Выбранная программа уже имеет значения норматива.'],
            [['programm_id', 'standard_basic', 'standard_basic_ratio'], 'required'],
            [['standard_basic', 'standard_basic_ratio'], 'number'],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::className(), 'targetAttribute' => ['programm_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'programm_id' => 'Программа обучения',
            'standard_basic' => 'Норматив базовый, руб.',
            'standard_basic_ratio' => 'Коэффициент к базовому нормативу',
            'standard' => 'Норматив, руб.',
        ];
    }

    /**
     * Gets query for [[Programm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramm()
    {
        return $this->hasOne(EducationProgramm::className(), ['id' => 'programm_id']);
    }

    public function getStandard()
    {
        return $this->standard_basic * $this->standard_basic_ratio;
    }

}
