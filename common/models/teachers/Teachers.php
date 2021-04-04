<?php

namespace common\models\teachers;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\db\ActiveRecord;
use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use common\models\guidejob\Level;
use common\models\guidejob\Position;
use common\models\user\UserCommon;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "teachers".
 *
 * @property int $id
 * @property int $user_common_id
 * @property int $position_id
 * @property int $level_id
 * @property string $tab_num
 * @property int $year_serv
 * @property int $year_serv_spec
 * @property int $date_serv
 * @property int $date_serv_spec
 * @property float $bonus_summ
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $version
 * @property string $bonus_list
 * @property string $department_list
 *
 * @property TeachersLevel $level
 * @property TeachersPosition $position
 * @property UserCommon $status
 */
class Teachers extends ActiveRecord
{
    use DateTimeTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers';
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
                'attributes' => ['bonus_list', 'department_list'],
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['date_serv', 'date_serv_spec'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position_id', 'department_list', 'level_id'], 'required'],
            [['position_id', 'level_id', 'user_common_id'], 'integer'],
            [['tab_num'], 'string', 'max' => 16],
            ['bonus_summ', 'safe'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['level_id'], 'exist', 'skipOnError' => true, 'targetClass' => Level::class, 'targetAttribute' => ['level_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::class, 'targetAttribute' => ['position_id' => 'id']],
            [['bonus_list', 'department_list', 'date_serv', 'date_serv_spec'], 'safe'],
            [['year_serv', 'year_serv_spec'], 'safe'],
            ['year_serv', 'compareSpec'],

        ];
    }

     /**
     * Сравнение общего стажа со стажем по специальности
     * @return  mixed
     */
     public function compareSpec()
    {
        if (!$this->hasErrors()) {

            if ($this->year_serv < $this->year_serv_spec) {
                $this->addError('year_serv_spec', Yii::t('art/teachers', 'Experience in the specialty can not be more than the total experience.'));
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
            'position_id' => Yii::t('art/teachers', 'Position'),
            'level_id' => Yii::t('art/teachers', 'Level'),
            'tab_num' => Yii::t('art/teachers', 'Tab Num'),
            'date_serv' => Yii::t('art/teachers', 'Date Serv'),
            'date_serv_spec' => Yii::t('art/teachers', 'Date Serv Spec'),
            'bonus_list' => Yii::t('art/teachers', 'Bonus'),
            'bonus_summ' => Yii::t('art/teachers', 'Bonus Summ %'),
            'department_list' => Yii::t('art/guide', 'Department'),
            'year_serv' => Yii::t('art/teachers', 'Year Serv'),
            'year_serv_spec' => Yii::t('art/teachers', 'Year Serv Spec'),
            'teachersFullName' => Yii::t('art', 'Full Name'),
            'gridDepartmentSearch' => Yii::t('art/guide', 'Department'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'userStatus' => Yii::t('art', 'Status'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel()
    {
        return $this->hasOne(Level::class, ['id' => 'level_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id' => 'position_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersActivity()
    {
        return $this->hasMany(TeachersActivity::class, ['teachers_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserCommon::class, ['id' => 'user_common_id']);
    }
    public function getUserStatus()
    {
        return $this->user->status;
    }
    /**
     * Геттер полного имени юзера
     */
    public function getTeachersFullName()
    {
        return $this->user->fullName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        $model = UserCommon::findOne($this->user_common_id);
        if(!$model->delete()){
            return false;
        }
        return parent::beforeDelete();
    }
}
