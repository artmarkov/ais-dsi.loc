<?php

namespace common\models\teachers;

use common\models\guidejob\Direction;
use common\models\guidejob\Stake;
use common\models\guidejob\Work;
use Yii;


/**
 * This is the model class for table "{{%teachers_activity}}".
 *
 * @property int $id
 * @property int $teachers_id
 * @property int $work_id
 * @property int $direction_id
 * @property int $stake_id
 *
 * @property TeachersWork $work
 * @property TeachersDirection $direction
 * @property TeachersStake $stake
 * @property Teachers $teachers
 */
class TeachersActivity extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teachers_activity}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['work_id', 'direction_id', 'stake_id'], 'required'],
            [['teachers_id', 'work_id', 'direction_id', 'stake_id'], 'integer'],
//            ['work_id', 'unique', 'targetAttribute' => ['teachers_id', 'work_id'], 'message' => Yii::t('art/teachers', 'The main activity may not be the same as the secondary one.')],
//            ['direction_id', 'compareDirection'],
            [['work_id'], 'exist', 'skipOnError' => true, 'targetClass' => Work::className(), 'targetAttribute' => ['work_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['stake_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stake::className(), 'targetAttribute' => ['stake_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    /**
     * Проверка на одинаковость полей direction_id
     * @return  mixed
     */
    public function compareDirection()
    {
        if (!$this->hasErrors()) {
           $count = self::find()
                ->where('id != :id', ['id'=>$this->id])
                ->andWhere(['teachers_id' => $this->teachers_id])
                ->andWhere(['direction_id' => $this->direction_id])
                ->count();
            if ($count != 0) {
                $this->addError('direction_id', Yii::t('art/teachers', 'The primary activity cannot but coincide with the secondary one.'));
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers ID'),
            'work_id' => Yii::t('art/teachers', 'Name Work'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'stake_id' => Yii::t('art/teachers', 'Name Stake'),
        ];
    }

    /**
     * Gets query for [[Work]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWork()
    {
        return $this->hasOne(Work::className(), ['id' => 'work_id']);
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Stake]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStake()
    {
        return $this->hasOne(Stake::className(), ['id' => 'stake_id']);
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::className(), ['id' => 'teachers_id']);
    }
}
