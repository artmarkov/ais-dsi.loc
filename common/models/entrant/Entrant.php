<?php

namespace common\models\entrant;

use artsoft\Art;
use artsoft\behaviors\ArrayFieldBehavior;
use common\models\education\EducationProgrammLevel;
use common\models\students\Student;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subject\SubjectForm;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;
use yii\db\Query;

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
 * @property int|null $subject_id, Назначена специальность
 * @property int|null $course Назначен курс
 * @property int|null $subject_form_id Назначен вид обучения(бюджет, внебюджет)
 * @property int $status Статус (В ожидании испытаний, Испытания открыты, Испытания завершены)
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
            [['student_id', 'comm_id', 'group_id'], 'required'],
            [['student_id', 'comm_id', 'group_id', 'decision_id', 'programm_id', 'subject_id', 'course', 'subject_form_id', 'status', 'version'], 'integer'],
            [['last_experience', 'remark'], 'string', 'max' => 127],
            [['decision_id'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 0],
            [['subject_list'], 'safe'],
            [['student_id'], 'unique', 'targetAttribute' => ['student_id', 'comm_id'], 'message' => 'Ученик уже записан на экзамен.'],
            [['reason'], 'string', 'max' => 1024],
            [['programm_id', 'course', 'subject_form_id'], 'required', 'when' => function ($model) {
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
            [['subject_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectForm::class, 'targetAttribute' => ['subject_form_id' => 'id']],
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
            'subject_id' => Yii::t('art/guide', 'Education Specializations'),
            'course' => Yii::t('art/guide', 'Course Reason'),
            'subject_form_id' => Yii::t('art/guide', 'Subject Form'),
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
        if (isset($this->entrantMembers)) {
            foreach ($this->entrantMembers as $members) {
                if (isset($members->entrantTest)) {
                    foreach ($members->entrantTest as $test) {
                        if (isset($test->entrantMark)) {
                            $mark += $test->entrantMark->mark_value;
                            $i++;
                        }
                    }
                }
            }
        }
        return $i != 0 ? round($mark / $i, 2) : 0;
    }

    /**
     * @return array
     */
    public function getEntrantMembersDefault()
    {
        $models = $modelsComm = [];
        $modelComm = $this->comm;
        $userId = Yii::$app->user->identity->getId();

        if (\artsoft\models\User::hasPermission('fullEntrantAccess') && Art::isBackend()) {
            $modelsComm = $modelComm->members_list;
        } elseif (in_array($userId, $modelComm->members_list)) {
            $modelsComm = [$userId];
        }

        foreach ($modelsComm as $item => $members_id) {
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
    public static function getStatusList()
    {
        return array(
            0 => 'В ожидании испытаний',
            1 => 'Испытания открыты',
            2 => 'Испытания завершены',
        );
    }

    /**
     * @param string $val
     * @return mixed|string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();
        return isset($ar[$val]) ? $ar[$val] : $val;
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
        if (!$comm_id) {
            return [];
        }
        return \yii\helpers\ArrayHelper::map(self::getCommGroupById($comm_id), 'id', 'name');
    }

    /**
     * @param $comm_id
     * @return array|EntrantGroup[]|\yii\db\ActiveRecord[]
     */
    public static function getCommGroupById($comm_id)
    {
        if (!$comm_id) {
            return [];
        }
        return EntrantGroup::find()
            ->select(['id', 'CONCAT(name, \' - \', to_char(to_timestamp(timestamp_in), \'DD.MM.YYYY HH24:mi\')) as name'])
            ->andWhere(['=', 'comm_id', $comm_id])->orderBy('timestamp_in')->asArray()->all();
    }

    /**
     * @return array
     */
    public static function getEntrantList()
    {
        $qyery = (new Query())->from('entrant_view')
            ->select(['student_id', 'CONCAT(fullname, \' - \', to_char(to_timestamp(birth_date), \'DD.MM.YYYY\'), \' (\', birth_date_age, \' лет)\') as fio'])
            ->distinct()
            ->all();
        return \yii\helpers\ArrayHelper::map($qyery, 'student_id', 'fio');

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
     * @param $comm_id
     * @return array
     * @throws Exception
     */
    public static function getCommSubjectList($comm_id)
    {
        if (!$comm_id) {
            return [];
        }
        return \yii\helpers\ArrayHelper::map(self::getCommSubjectListById($comm_id), 'id', 'name');
    }

    /**
     * Определение дисциплин для выбранных отделов комиссии
     * @param $comm_id
     * @return array
     * @throws Exception
     */
    public static function getCommSubjectListById($comm_id)
    {
        if (!$comm_id) {
            return [];
        }
        $entrantComm = EntrantComm::findOne($comm_id);
        $department_list = implode(',', $entrantComm->department_list);
        $funcSql = <<< SQL
            SELECT DISTINCT subject_id AS id,
                  subject_name AS name
            FROM subject_view
            WHERE department_id = ANY (string_to_array('{$department_list}', ',')::int[]) 
            ORDER BY subject_name;
		
SQL;
        return Yii::$app->db->createCommand($funcSql)->queryAll();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->decision_id == 1) {
            $this->makeStadylan();
            $this->reason = null;
            $this->status = 2;
        } elseif ($this->decision_id == 2) {
            $this->deleteStadylan();
            $this->programm_id = null;
            $this->subject_id = null;
            $this->course = null;
            $this->subject_form_id = null;
            $this->status = 2;
        } else {
            $this->deleteStadylan();
            $this->reason = null;
            $this->programm_id = null;
            $this->subject_id = null;
            $this->course = null;
            $this->subject_form_id = null;
        }
        return parent::beforeSave($insert);
    }

    public function deleteStadylan()
    {
        if (!$this->course || !$this->programm_id) {
            return false;
        }
        $model = Studyplan::find()->where(['=', 'programm_id', $this->programm_id])
            ->andWhere(['=', 'plan_year', $this->comm->plan_year])
            ->andWhere(['=', 'course', $this->course])
            ->andWhere(['=', 'student_id', $this->student_id])->one();

        if ($model) {
            $model->delete(false);
        }
    }

    public function makeStadylan()
    {
        ini_set('memory_limit', '1024');
        $exists = Studyplan::find()->where(['=', 'programm_id', $this->programm_id])
            ->andWhere(['=', 'plan_year', $this->comm->plan_year])
            ->andWhere(['=', 'course', $this->course])
            ->andWhere(['=', 'student_id', $this->student_id])->exists();

        if (!$exists) {
            $transaction = \Yii::$app->db->beginTransaction();
            $model = new Studyplan();
            $model->setAttributes(
                [
                    'programm_id' => $this->programm_id,
                    'subject_form_id' => $this->subject_form_id,
                    'course' => $this->course,
                    'student_id' => $this->student_id,
                    'plan_year' => $this->comm->plan_year,
                ]
            );
            try {
                $modelProgrammLevel = EducationProgrammLevel::find()
                    ->where(['programm_id' => $this->programm_id])
                    ->andWhere(['course' => $this->course])
                    ->one();
                if ($modelProgrammLevel) {
                    $model->copyAttributes($modelProgrammLevel);
                }

                if ($flag = $model->save(false)) {

                    if (isset($modelProgrammLevel->educationProgrammLevelSubject)) {
                        $modelsSubTime = $modelProgrammLevel->educationProgrammLevelSubject;
                        foreach ($modelsSubTime as $modelSubTime) {
                            $modelSub = new StudyplanSubject();
                            if($modelSubTime->subject_cat_id = 1000) {
                                $modelSubTime->subject_id = $this->subject_id;
                            }
                            $modelSub->copyAttributes($model, $modelSubTime);

                            if (!($flag = $modelSub->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                }

                if ($flag) {
                    $transaction->commit();
                    return true;
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                return false;
            }
        }
    }

    public static function runActivate($id)
    {
        $model = self::findOne($id);
        if (!$model) {
            return false;
        }
        $model->status = 1;
        if ($model->save(false)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public static function runDeactivate($id)
    {
        $model = self::findOne($id);
        if (!$model) {
            return false;
        }
        $model->status = 2;
        if ($model->save(false)) {
            return true;
        }
        return false;
    }
}
