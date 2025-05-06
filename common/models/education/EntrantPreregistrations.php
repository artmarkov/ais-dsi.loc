<?php

namespace common\models\education;

use artsoft\helpers\ArtHelper;
use artsoft\models\User;
use artsoft\widgets\Notice;
use common\models\students\Student;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "entrant_preregistrations".
 *
 * @property int $id
 * @property int $entrant_programm_id Выбранная программа для предварительной записи
 * @property int $student_id Учетная запись ученика-кандидата
 * @property int $plan_year Учебный год приема ученика
 * @property int $reg_vid Вид записи (Список: для приема, в резерв)
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 *
 * @property EntrantProgramm $entrantProgramm
 * @property Student $student
 */
class EntrantPreregistrations extends \artsoft\db\ActiveRecord
{
    const REG_ENTRANT = 1;
    const REG_RESERVE = 2;

    const REG_STATUS_DRAFT = 0;
    const REG_STATUS_STUDENT = 1;
    const REG_STATUS_OUTSIDE = 3;
    const REG_PLAN_CLOSED = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_preregistrations';
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
            [['plan_year', 'entrant_programm_id', 'student_id', 'reg_vid'], 'required'],
            [['plan_year', 'entrant_programm_id', 'student_id', 'reg_vid', 'status'], 'integer'],
            [['entrant_programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntrantProgramm::class, 'targetAttribute' => ['entrant_programm_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['entrant_programm_id'], 'unique', 'targetAttribute' => [/*'entrant_programm_id',*/ 'plan_year', 'student_id'],
                'message' => 'Ученик уже записан на программу в рамках предварительной записи.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'entrant_programm_id' => Yii::t('art/guide', 'Entrant Programms'),
            'student_id' => Yii::t('art/student', 'Student'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
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
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    public static function getRegList()
    {
        return array(
            self::REG_ENTRANT => Yii::t('art/guide', 'Reg Entrant'),
            self::REG_RESERVE => Yii::t('art/guide', 'Reg Reserve'),
        );
    }

    /**
     * getDocStatusList
     * @return array
     */
    public static function getRegStatusList()
    {
        return array(
            self::REG_STATUS_DRAFT => Yii::t('art', 'Draft'),
            self::REG_STATUS_STUDENT => Yii::t('art/guide', 'Accepted for training'),
            self::REG_STATUS_OUTSIDE => Yii::t('art/guide', 'Refused admission'),
            self::REG_PLAN_CLOSED => Yii::t('art/guide', 'Plan closed'),
        );
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getRegStatusValue($val)
    {
        $ar = self::getRegStatusList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return array
     */
    public static function getEntrantPreregistrationList()
    {
       $query = self::find()->innerJoin('students', 'entrant_preregistrations.student_id = students.id')
            ->innerJoin('user_common', 'user_common.id = students.user_common_id')
            ->andWhere(['in', 'user_common.user_category', UserCommon::USER_CATEGORY_STUDENTS])// только ученики
            ->select(['students.id as id', "CONCAT(user_common.last_name, ' ',user_common.first_name, ' ',user_common.middle_name) AS name"])
            ->orderBy('user_common.last_name')
            ->asArray()->all();
        return ArrayHelper::map($query, 'id', 'name');
    }

    public function sendMessage($email)
    {
        if ($email) {
            $textBody = 'Сообщение модуля "Предварительная регистрация на обучение" ' . PHP_EOL;
            $htmlBody = '<p><b>Сообщение модуля "Предварительная регистрация на обучение"</b></p>';

            $textBody .= 'Вы записали ребенка на программу обучения: ' . strip_tags(\common\models\education\EntrantProgramm::getEntrantProgrammValue($this->entrant_programm_id)) . PHP_EOL;
            $htmlBody .= '<p>Вы записали ребенка на программу обучения:' . strip_tags(\common\models\education\EntrantProgramm::getEntrantProgrammValue($this->entrant_programm_id)) . '</p>';

            $textBody .= 'Просьба написать по телефону в whats app или Telegram 8-926-350-17-97 с 10:00 - 18:00 с понедельника по пятницу для уточнения информации по обучению и оплате.' . PHP_EOL;
            $htmlBody .= '<p>Просьба написать по телефону в whats app или Telegram 8-926-350-17-97 с 10:00 - 18:00 с понедельника по пятницу для уточнения информации по обучению и оплате.' . '</p>';

            $textBody .= '--------------------------' . PHP_EOL;
            $textBody .= 'Сообщение создано автоматически. Отвечать на него не нужно.';
            $htmlBody .= '<hr>';
            $htmlBody .= '<p>Сообщение создано автоматически. Отвечать на него не нужно.</p>';

            return Yii::$app->mailqueue->compose()
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($email)
                ->setSubject('Сообщение с сайта ' . Yii::$app->name)
                ->setTextBody($textBody)
                ->setHtmlBody($htmlBody)
                ->queue();
        }
    }

    public static function getRegSummary($model_date)
    {
//        $data = $dates = [];
//        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
//        $timestamp_in = $timestamp[0];
//        $timestamp_out = $timestamp[1];
//
//        $attributes = ['subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name')];
//        $attributes += ['student_id' => Yii::t('art/student', 'Student')];
//
//        $lessonDates = LessonItemsProgressView::find()->select('lesson_date')->distinct()
//            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
//            ->andWhere(['=', 'subject_sect_id', $subject_sect_id])
//            ->andWhere(['=', 'subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id])
//            ->orderBy('lesson_date')
//            ->asArray()->all();
//        $modelsProgress = self::find()->where(['subject_sect_studyplan_id' => $model_date->subject_sect_studyplan_id])
//            ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
//            ->orderBy('sect_name')->all();
//
//        $modelsMarks = ArrayHelper::index(LessonItemsProgressView::find()
//            ->where(['between', 'lesson_date', $timestamp_in, $timestamp_out])
//            ->andWhere(['subject_sect_id' => $subject_sect_id])
//            ->andWhere(['=', 'subject_sect_studyplan_id', $model_date->subject_sect_studyplan_id])
//            ->all(), null, 'studyplan_subject_id');
//
//        // echo '<pre>' . print_r($modelsMarks, true) . '</pre>'; die();
//        foreach ($lessonDates as $id => $lessonDate) {
//            $date = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d.m.Y');
//            $label = Yii::$app->formatter->asDate($lessonDate['lesson_date'], 'php:d');
//            $attributes += [$date => $label];
//            $dates[] = $date;
//        }
//        foreach ($modelsProgress as $item => $modelProgress) {
//            $data[$item]['lesson_timestamp'] = $lessonDates;
//            $data[$item]['subject_sect_id'] = $modelProgress->subject_sect_id;
//            $data[$item]['subject_sect_studyplan_id'] = $modelProgress->subject_sect_studyplan_id;
//            $data[$item]['sect_name'] = $modelProgress->sect_name;
//            $data[$item]['studyplan_subject_id'] = $modelProgress->studyplan_subject_id;
//            $data[$item]['studyplan_id'] = $modelProgress->studyplan_id;
//            $data[$item]['student_id'] = $modelProgress->student_id;
//            $data[$item]['student_fio'] = $modelProgress->student_fio;
//
//            if (isset($modelsMarks[$modelProgress->studyplan_subject_id])) {
//                foreach ($modelsMarks[$modelProgress->studyplan_subject_id] as $id => $mark) {
//                    $date_label = Yii::$app->formatter->asDate($mark->lesson_date, 'php:d.m.Y');
//                    $data[$item][$date_label] = self::getEditableForm($date_label, $mark);
//                }
//            }
//        }

        echo '<pre>' . print_r($model_date, true) . '</pre>';
        die();
        return ['data' => $data, 'lessonDates' => $dates, 'attributes' => $attributes];
    }

    public function beforeSave($insert)
    {
        if ($this->status == self::REG_STATUS_STUDENT) {
            $entrantProgramm = $this->entrantProgramm;
            $exists = Studyplan::find()->where(['=', 'programm_id', $entrantProgramm->programm_id])
                ->andWhere(['=', 'plan_year', $this->plan_year])
                ->andWhere(['=', 'course', $entrantProgramm->course])
                ->andWhere(['=', 'student_id', $this->student_id])->exists();

            if (!$exists) {
                $transaction = \Yii::$app->db->beginTransaction();
                $model = new Studyplan();
                $model->setAttributes(
                    [
                        'programm_id' => $entrantProgramm->programm_id,
                        'subject_form_id' => $entrantProgramm->subject_type_id,
                        'course' => $entrantProgramm->course,
                        'student_id' => $this->student_id,
                        'plan_year' => $this->plan_year,
                    ]
                );
                try {
                    $modelProgrammLevel = EducationProgrammLevel::find()
                        ->where(['programm_id' => $entrantProgramm->programm_id])
                        ->andWhere(['course' => $entrantProgramm->course])
                        ->one();
                    if ($modelProgrammLevel) {
                        $model->copyAttributes($modelProgrammLevel);
                    }
                    if ($flag = $model->save(false)) {

                        if (isset($modelProgrammLevel->educationProgrammLevelSubject)) {
                            $modelsSubTime = $modelProgrammLevel->educationProgrammLevelSubject;
                            foreach ($modelsSubTime as $modelSubTime) {
                                $modelSub = new StudyplanSubject();
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
        return parent::beforeSave($insert);
    }
}
