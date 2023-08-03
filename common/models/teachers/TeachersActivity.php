<?php

namespace common\models\teachers;

use common\models\guidejob\Cost;
use common\models\guidejob\Direction;
use common\models\guidejob\DirectionVid;
use common\models\guidejob\Stake;
use common\models\guidejob\Work;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;
use yii\db\Query;


/**
 * This is the model class for table "teachers_activity".
 *
 * @property int $id
 * @property int $teachers_id
 * @property int $direction_vid_id
 * @property int $direction_id
 * @property int $stake_id
 *
 * @property TeachersDirectionVid $directionVid
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
        return 'teachers_activity';
    }

    /**
     * @inheritdoc
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
            [['direction_vid_id', 'direction_id', 'stake_id'], 'required'],
            [['teachers_id', 'direction_vid_id', 'direction_id', 'stake_id'], 'integer'],
//            ['work_id', 'unique', 'targetAttribute' => ['teachers_id', 'work_id'], 'message' => Yii::t('art/teachers', 'The main activity may not be the same as the secondary one.')],
//            ['direction_id', 'compareDirection'],
            [['direction_vid_id'], 'exist', 'skipOnError' => true, 'targetClass' => DirectionVid::class, 'targetAttribute' => ['direction_vid_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['stake_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stake::class, 'targetAttribute' => ['stake_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    public function optimisticLock()
    {
        return 'version';
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
                $this->addError('direction_id', Yii::t('art/teachers', 'The primary activity cannot but coincide with the secondary one'));
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
            'direction_vid_id' => Yii::t('art/teachers', 'Name Direction Vid'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'stake_id' => Yii::t('art/teachers', 'Name Stake'),
        ];
    }

    /**
     * Gets query for [[DirectionVid]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionVid()
    {
        return $this->hasOne(DirectionVid::class, ['id' => 'direction_vid_id']);
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Stake]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStake()
    {
        return $this->hasOne(Stake::class, ['id' => 'stake_id']);
    }

    public function getCost()
    {
        return $this->hasMany(Cost::class, ['id' => 'stake_id']);
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

    public static function getDirectionListById($teachers_id)
    {
        if (!$teachers_id) {
            return [];
        }
        return self::find()
            ->innerJoin('guide_teachers_direction', 'guide_teachers_direction.id = teachers_activity.direction_id')
            ->select('guide_teachers_direction.id as id, guide_teachers_direction.name as name')
            ->where(['=', 'teachers_id', $teachers_id])
            ->asArray()
            ->all();
    }
    public static function getDirectionListForTeachers($teachers_id = null)
    {
        return \yii\helpers\ArrayHelper::map(self::getDirectionListById($teachers_id), 'id', 'name');
    }

    /**
     * По умолчанию для depdrop
     * @param null $teachers_id
     * @return mixed|null
     */
    public static function getDirectionInitForTeachers($teachers_id = null)
    {
        $modelsDir = self::getDirectionListById($teachers_id);
        if(count($modelsDir) != 1) {
            return null;
        }
        $direction_id = $modelsDir[0]['id'];
        return $direction_id;
    }

    /**
     * По умолчанию для depdrop
     * @param $teachers_id
     * @param $direction_id
     * @return mixed|null
     */
    public static function getDirectionVidInitForTeachers($teachers_id, $direction_id)
    {
        if (!$teachers_id && !$direction_id) {
            return null;
        }
        $modelsVid = self::getDirectionVidListById($teachers_id, $direction_id);
        $direction_vid_id = $modelsVid[0]['id'] ?? null;
        return $direction_vid_id;
    }

    public static function getDirectionVidListById($teachers_id, $direction_id)
    {
        if (!$teachers_id && !$direction_id) {
            return [];
        }
        return self::find()
            ->innerJoin('guide_teachers_direction_vid', 'guide_teachers_direction_vid.id = teachers_activity.direction_vid_id')
            ->select('guide_teachers_direction_vid.id as id, guide_teachers_direction_vid.name as name')
            ->where(['=', 'teachers_id', $teachers_id])
            ->andWhere(['=', 'direction_id', $direction_id])
            ->asArray()
            ->all();
    }
    public static function getDirectionVidListForTeachers($teachers_id = null, $direction_id = null)
    {
        return \yii\helpers\ArrayHelper::map(self::getDirectionVidListById($teachers_id, $direction_id), 'id', 'name');
    }
}
