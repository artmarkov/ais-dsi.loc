<?php

namespace common\models\entrant;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\models\User;
use common\models\students\Student;
use common\models\studyplan\Studyplan;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "entrant".
 *
 * @property int $id
 * @property int $student_id
 * @property int $comm_id Комиссия Id
 * @property int $group_id Группа экзаменационная
 * @property string $subject_list Выбранный инструмент
 * @property string $last_experience Где обучался ранее
 * @property string $remark Примечание
 * @property int|null $decision_id Решение комиссии (Рекомендован, Не рекомендован)
 * @property string|null $reason Причина комиссии
 * @property int|null $unit_reason_id Рекомендовано отделение
 * @property int|null $plan_id Назначен учебный план
 * @property int|null $course Назначен курс
 * @property int|null $type_id Назначен вид обучения(бюджет, внебюджет)
 * @property int $status Статус (Активная, Не активная)
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property EntrantComm $comm
 * @property EntrantGroup $group
 * @property Students $student
 * @property Studyplan $studyplan
 * @property EntrantTest[] $entrantTests
 */
class Entrant extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant';
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
                'attributes' => ['subject_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'comm_id', 'group_id', 'last_experience', 'subject_list'], 'required'],
            [['student_id', 'comm_id', 'group_id', 'decision_id', 'unit_reason_id', 'plan_id', 'course', 'type_id', 'status', 'version'], 'integer'],
            [['last_experience', 'remark'], 'string', 'max' => 127],
            [['subject_list'], 'safe'],
            [['reason'], 'string', 'max' => 1024],
            [['unit_reason_id', 'plan_id', 'course', 'type_id'], 'required', 'when' => function ($model) {
                return $model->decision_id === '1';
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[name=\"Entrant[decision_id]\"]:checked').val() === '1';
                            }"],
            [['reason'], 'required', 'when' => function ($model) {
                return $model->decision_id === '2';
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[name=\"Entrant[decision_id]\"]:checked').val() === '2';
                            }"],
            [['comm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntrantComm::className(), 'targetAttribute' => ['comm_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntrantGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Studyplan::className(), 'targetAttribute' => ['plan_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'student_id' => Yii::t('art/student', 'Student'),
            'comm_id' => Yii::t('art/guide', 'Commission'),
            'group_id' => Yii::t('art/guide', 'Group'),
            'subject_list' => Yii::t('art/guide', 'Subject List'),
            'last_experience' => Yii::t('art/guide', 'Last Experience'),
            'remark' => Yii::t('art/guide', 'Remark'),
            'decision_id' => Yii::t('art/guide', 'Decision'),
            'reason' => Yii::t('art/guide', 'Reason'),
            'unit_reason_id' => Yii::t('art/guide', 'Unit Reason'),
            'plan_id' => Yii::t('art/guide', 'Plan Reason'),
            'course' => Yii::t('art/guide', 'Course Reason'),
            'type_id' => Yii::t('art/guide', 'Type Reason'),
            'status' => Yii::t('art', 'Status'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Comm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComm()
    {
        return $this->hasOne(EntrantComm::className(), ['id' => 'comm_id']);
    }

    /**
     * Gets query for [[Group]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(EntrantGroup::className(), ['id' => 'group_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[Plan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplan()
    {
        return $this->hasOne(Studyplan::className(), ['id' => 'plan_id']);
    }

    /**
     * Gets query for [[EntrantTests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantTests()
    {
        return $this->hasMany(EntrantTest::className(), ['entrant_id' => 'id']);
    }

    public static function getDecisionList()
    {
        return array(
            0 => 'Не обработано',
            1 => 'Рекомендован',
            2 => 'Не рекомендован',
        );
    }

    public static function getDecisionValue($val)
    {
        $ar = self::getDecisionList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return array
     */
    public static function getCommGroupList($comm_id)
    {
        return \yii\helpers\ArrayHelper::map(EntrantGroup::find()->andWhere(['=', 'comm_id', $comm_id])->all(), 'id', 'name');
    }

    public static function getCommGroupValue($comm_id, $val)
    {
        $ar = self::getCommGroupList($comm_id);
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->decision_id == 1) {
            $this->reason = null;
        } elseif ($this->decision_id == 2) {
            $this->unit_reason_id = null;
            $this->plan_id = null;
            $this->course = null;
            $this->type_id = null;
        } else {
            $this->reason = null;
            $this->unit_reason_id = null;
            $this->plan_id = null;
            $this->course = null;
            $this->type_id = null;
        }
        return parent::beforeSave($insert);
    }
}
