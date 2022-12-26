<?php

namespace common\models\entrant;

use artsoft\behaviors\ArrayFieldBehavior;
use common\models\students\Student;
use common\models\studyplan\Studyplan;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;

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
 * @property int|null $programm_id Назначена программа
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
 * @property EntrantMembers[] $entrantMembers
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
            [['student_id', 'comm_id', 'group_id', 'decision_id',  'programm_id', 'course', 'type_id', 'status', 'version'], 'integer'],
            [['last_experience', 'remark'], 'string', 'max' => 127],
            [['decision_id'], 'default', 'value' => 0],
            [['subject_list'], 'safe'],
            [['reason'], 'string', 'max' => 1024],
            [['programm_id', 'course', 'type_id'], 'required', 'when' => function ($model) {
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
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => Studyplan::className(), 'targetAttribute' => ['programm_id' => 'id']],
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
            'programm_id' => Yii::t('art/guide', 'Plan Reason'),
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
        return $this->hasOne(Studyplan::className(), ['id' => 'programm_id']);
    }

    /**
     * Gets query for [[EntrantMembers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrantMembers()
    {
        return $this->hasMany(EntrantMembers::className(), ['entrant_id' => 'id']);
    }

    /**
     * средняя оценка абитуриента
     * @return float|int
     */
    public function getEntrantMidMark()
    {
        $mark = 0;
        $i = 0;
        if(isset($this->entrantMembers)) {
            foreach ($this->entrantMembers as $members) {
                if(isset($members->entrantTest)) {
                    foreach ($members->entrantTest as $test) {
                        if (isset($test->entrantMark)) {
                            $mark += $test->entrantMark->mark_value;
                            $i++;
                        }
                    }
                }
            }
        }
        return $i != 0 ? round($mark/$i, 2) : 0;
    }

    /**
     * @return array
     */
    public function getEntrantMembersDefault()
    {
        $models = [];
        $modelComm = $this->comm;
        foreach ($modelComm->members_list as $item => $members_id){
            $model = EntrantMembers::find()->andWhere(['members_id' => $members_id])->andWhere(['entrant_id' => $this->id])->one() ?: new EntrantMembers();
            $model->members_id = $members_id;
            $model->entrant_id = $this->id;
            $models[] = $model;
        }
        return $models;
    }

    /**
     * @return array
     */
    public static function getDecisionList()
    {
        return array(
            0 => 'Не обработано',
            1 => 'Рекомендован',
            2 => 'Не рекомендован',
        );
    }

    /**
     * @param $val
     * @return mixed
     */
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

    /**
     * @param $comm_id
     * @param $val
     * @return mixed
     */
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
            $this->programm_id = null;
            $this->course = null;
            $this->type_id = null;
        } else {
            $this->reason = null;
            $this->programm_id = null;
            $this->course = null;
            $this->type_id = null;
        }
        return parent::beforeSave($insert);
    }
}
