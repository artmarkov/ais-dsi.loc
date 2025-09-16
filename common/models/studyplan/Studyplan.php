<?php

namespace common\models\studyplan;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\PriceHelper;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\education\CostEducation;
use common\models\education\EducationProgramm;
use common\models\education\EducationProgrammLevel;
use common\models\own\Invoices;
use common\models\parents\Parents;
use common\models\students\Student;
use common\models\subject\Subject;
use common\models\schedule\SubjectScheduleStudyplanView;
use common\models\subject\SubjectForm;
use common\models\subjectsect\SubjectSect;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadStudyplanView;
use common\models\teachers\TeachersLoadView;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use function morphos\Russian\inflectName;

/**
 * This is the model class for table "studyplan".
 *
 * @property int $id
 * @property int $student_id
 * @property int $programm_id
 * @property int $subject_form_id
 * @property int|null $course
 * @property int|null $plan_year
 * @property string|null $description
 * @property float|null $year_time_total
 * @property float|null $cost_month_total
 * @property float|null $cost_year_total
 * @property int $created_at
 * @property int $doc_date
 * @property int $doc_contract_start
 * @property int $doc_contract_end
 * @property int $doc_signer
 * @property int $doc_received_flag
 * @property int $doc_sent_flag
 * @property int $early_flag // Досрочное завершение - выпускник
 * @property int $cond_flag // Условное открытие плана
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 * @property int $status_reason
 * @property int $mat_capital_flag Возможность использования мат. капиталла
 *
 * @property EducationProgramm $programm
 * @property Student $student
 * @property Parents $parent
 * @property SubjectForm $subjectForm
 */
class Studyplan extends \artsoft\db\ActiveRecord
{

// Шаблоны документов
    const template_csf = 'document/contract_student_free_2.docx';
    const template_cs = 'document/contract_student_new.docx';
    const template_sr = 'document/studyplan_reference.docx';
    const template_cs_mk = 'document/contract_student_new-mk.docx';
    const template_ss = 'document/statement_student.docx';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['doc_date', 'doc_contract_start', 'doc_contract_end'],
            ],
            [
                'class' => FileManagerBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'programm_id', 'subject_form_id', 'course', 'plan_year'], 'required'],
//            [['doc_date', 'doc_contract_start', 'doc_contract_end', 'doc_signer'], 'required', 'when' => function ($model) {
//                return !$model->isNewRecord;
//            }],
            ['doc_contract_start', 'compareDate'],
            [['student_id', 'programm_id', 'course', 'plan_year', 'subject_form_id', 'status', 'version', 'status_reason', 'mat_capital_flag'], 'integer'],
            [['doc_signer', 'doc_received_flag', 'doc_sent_flag', 'early_flag', 'cond_flag'], 'integer'],
            [['doc_date', 'doc_contract_start', 'doc_contract_end'], 'safe'],
            ['doc_date', 'default', 'value' => date('d.m.Y')],
            [['description'], 'string', 'max' => 1024],
            [['year_time_total', 'cost_month_total', 'cost_year_total'], 'number'],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['programm_id' => 'id']],
            [['subject_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectForm::class, 'targetAttribute' => ['subject_form_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['doc_signer'], 'exist', 'skipOnError' => true, 'targetClass' => Parents::class, 'targetAttribute' => ['doc_signer' => 'id']],
        ];
    }

    /**
     * сравнение даты начала и окончания/ дата окончания должна быть меньше даты начала
     */
    public function compareDate()
    {
        if (!$this->hasErrors() && $this->doc_contract_start && $this->doc_contract_end) {

            if (Yii::$app->formatter->asTimestamp($this->doc_contract_end) < Yii::$app->formatter->asTimestamp($this->doc_contract_start)) {
                $this->addError('doc_contract_start', 'Дата начала действия договора не может быть больше даты окончания.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/studyplan', 'ID'),
            'student_id' => Yii::t('art/student', 'Student'),
            'studentFio' => Yii::t('art/student', 'Student'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'programmName' => Yii::t('art/studyplan', 'Education Programm'),
            'educationCatId' => Yii::t('art/guide', 'Education Cat'),
            'subject_form_id' => Yii::t('art/guide', 'Subject Form'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'description' => Yii::t('art', 'Description'),
            'year_time_total' => Yii::t('art/guide', 'Year Time Total'),
            'cost_month_total' => Yii::t('art/guide', 'Cost Month Total'),
            'cost_year_total' => Yii::t('art/guide', 'Cost Year Total'),
            'doc_date' => Yii::t('art/guide', 'Doc Date'),
            'doc_contract_start' => Yii::t('art/guide', 'Doc Contract Start'),
            'doc_contract_end' => Yii::t('art/guide', 'Doc Contract End'),
            'doc_signer' => Yii::t('art/guide', 'Doc Signer'),
            'doc_received_flag' => Yii::t('art/guide', 'Doc Received'),
            'doc_sent_flag' => Yii::t('art/guide', 'Doc Sent'),
            'early_flag' => Yii::t('art/guide', 'Early_Education'),
            'cond_flag' => Yii::t('art/guide', 'Cond_Education'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
            'version' => Yii::t('art', 'Version'),
            'status_reason' => Yii::t('art/studyplan', 'Status Reason'),
            'mat_capital_flag' => Yii::t('art/studyplan', 'Mat Capital'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => 'План открыт',
            self::STATUS_INACTIVE => 'План закрыт',
        );
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusReasonList()
    {
        return array(
            0 => 'Не задано',
            1 => 'Переведен в следующий класс',
            2 => 'Повторение учебной программы',
            3 => 'Окончание учебной программы',
            4 => 'Завершение обучения(выпуск)',
            5 => 'Отчисление',
        );
    }

    /**
     * getStatusValue
     * @param string $val
     * @return string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * getStatusValue
     * @param string $val
     * @return string
     */
    public static function getStatusReasonValue($val)
    {
        $ar = self::getStatusReasonList();
        return isset($ar[$val]) ? $ar[$val] : $val;
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

    public function getProgrammName()
    {
        return $this->programm->short_name;
    }

    public function getEducationCatId()
    {
        return $this->programm->education_cat_id;
    }

    public function getSubjectForm()
    {
        return $this->hasOne(SubjectForm::class, ['id' => 'subject_form_id']);
    }

    public function getSubjectFormName()
    {
        return $this->subjectForm ? $this->subjectForm->name : null;
    }

    /**
     * Геттер категория
     * @return array
     */
    public function getForm()
    {
        return $this->subject_form_id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanSubject()
    {
        return $this->hasMany(StudyplanSubject::class, ['studyplan_id' => 'id'])->innerJoin('guide_subject_category', 'guide_subject_category.id = studyplan_subject.subject_cat_id')->orderBy('sort_order, subject_vid_id');
    }

    /**
     * @return false|int|string|null
     */
    public function getSpecialityId()
    {
        return $this->getStudyplanSubject()->select('subject_id')->andWhere(['=', 'subject_cat_id', 1000])->scalar() ?: null;
    }

    /**
     * @return string|null
     */
    public function getSpeciality()
    {
        $subject_id = $this->getSpecialityId();
        return $subject_id ? RefBook::find('subject_name')->getValue($subject_id) : '';
    }


//    /**
//     * Список нагрузок преподавателей
//     * @return array
//     */
//    public function getStudyplanTeachersLoad()
//    {
//        $data = [];
//        foreach ($this->studyplanSubject as $index => $modelStudyplanSubject){
//            $studyplanSubjectName = RefBook::find('subject_memo_2')->getValue($modelStudyplanSubject->id);
//            $data[$studyplanSubjectName] = $modelStudyplanSubject->getTeachersLoadsDisplay();
//        }
//        return $data;
//    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    public function getStudentFio()
    {
        return $this->student->getFullName();
    }

    public function getStudentPhone()
    {
        return $this->student->getUserPhone();
    }

    public function getParent()
    {
        return $this->hasOne(Parents::class, ['id' => 'doc_signer']);
    }

    /**
     * @param $category_id
     * @return array|Subject[]|\common\models\subject\SubjectQuery
     */
    public function getSubjectById($category_id)
    {
        $data = [];
        if ($category_id) {
            $data = Subject::find()->select(['id', 'name']);
            $data = $data->andFilterWhere(['like', 'category_list', $category_id]);
            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
            $data = $data->asArray()->all();
        }
        return $data;
    }

    /**
     * Получаем возможные дисциплины программы выбранной категории
     * @param $category_id
     * @return array|\common\models\subject\SubjectQuery
     */
    public function getSubjectByCategory($category_id)
    {
        $data = [];
        if ($category_id) {
            $data = Subject::find()->select(['name', 'id']);
            $data = $data->andFilterWhere(['like', 'category_list', $category_id]);
            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
            $data = $data->orderBy('name');
            $data = $data->indexBy('id')->column();
        }
        return $data;
    }


    /**
     * формирование документов: Согласие на обработку пд и Договор об оказании услуг
     *
     * @param $template
     * @param $add_flag дополнительное соглашение по внебюджету у бюджетного плана
     * @throws \yii\base\InvalidConfigException
     */
    public function makeDocx($template, $add_flag = false)
    {
        $model = $this;
        if (!isset($model->parent)) {
            throw new NotFoundHttpException("The Parent was not found.");
        }
        $modelsDependence = $add_flag ? $model->getStudyplanSubject()->andWhere(['subject_type_id' => 1001])->all() : $model->getStudyplanSubject()->all();
        $modelProgrammLevel = EducationProgrammLevel::find()
            ->where(['programm_id' => $model->programm_id])
            ->andWhere(['course' => $model->course])
            ->one();
        $invoices = Invoices::findOne(1000);
        $save_as = str_replace(' ', '_', $model->student->fullName);
        $costGrandModel = CostEducation::findOne(['programm_id' => $model->programm_id]);
        $termMasteringGrand = $model->programm->term_mastering - $model->course + 1;
        $costYearGrandTotal = $model->cost_year_total * $termMasteringGrand;
        $costTotalDecline = $costGrandModel['standard_basic_ratio'] * $termMasteringGrand;
        $data[] = [
            'rank' => 'doc',
            'doc_date' => date('j', strtotime($model->doc_date)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_date))] . ' ' . date('Y', strtotime($model->doc_date)), // дата договора - месяц прописью
            'doc_date_start' => $model->doc_date, // дата начала действия согласия
            'doc_date_valid' => Yii::$app->formatter->asDate(strtotime('+5 year', strtotime($model->doc_date))), // дата окончания +5 лет действия согласия
            'doc_date_year' => date('y', strtotime($model->doc_date)), // год договора
            'doc_date_year_next' => date('y', strtotime($model->doc_date)) + 1, // год +1 договора
            'doc_signer' => $model->parent->fullName, // Полное имя подписанта-родителя
            'doc_signer_iof' => RefBook::find('parents_iof')->getValue($model->parent->id),
            'doc_signer_gen' => inflectName($model->parent->fullName, 'родительный'), // Полное имя подписанта-родителя родительный
            'doc_signer_dat' => inflectName($model->parent->fullName, 'дательный'), // Полное имя подписанта-родителя дательный
            'doc_student' => $model->student->fullName, // Полное имя ученика
            'doc_student_gen' => inflectName($model->student->fullName, 'родительный'), // Полное имя ученика родительный
            'doc_student_acc' => inflectName($model->student->fullName, 'винительный'), // Полное имя ученика винительный
            'doc_student_dat' => inflectName($model->student->fullName, 'дательный'), // Полное имя ученика дательный
            'student_birth_date' => $model->student->userBirthDate, // День рождения ученика
            'student_relation' => mb_strtolower(RefBook::find('parents_dependence_relation_name', $model->student_id)->getValue($model->parent->id), 'UTF-8'),
            'doc_contract_start' => date('j', strtotime($model->doc_contract_start)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_contract_start))] . ' ' . date('Y', strtotime($model->doc_contract_start)), // дата начала договора
            'doc_contract_end' => date('j', strtotime($model->doc_contract_end)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_contract_end))] . ' ' . date('Y', strtotime($model->doc_contract_end)), $model->doc_contract_end, // Дата окончания договора
            'programm_name' => $model->programm->name, // название программы
            'programm_level' => isset($modelProgrammLevel->level) ? $modelProgrammLevel->level->name : null, // уровень программы
            'term_mastering_pur' => $model->programm->term_mastering, // Срок обучения
            'term_mastering' => 'срок обучения: ' . $model->programm->term_mastering . ' лет(года)', // Срок обучения
            'term_mastering_op' => $termMasteringGrand, // Срок освоения образовательной программы
            'term_mastering_grand' => 'срок обучения: ' . $termMasteringGrand . ' лет(года)', // Срок освоения образовательной программы с грантом
            'course' => $model->course . ' класс',
            'course_pur' => $model->course,
            'year_time_total' => $model->year_time_total,
            'cost_grand' => $costGrandModel['standard_basic_ratio'],
            'cost_month_total' => $model->cost_month_total,
            'cost_month_total_str' => PriceHelper::num2str($model->cost_month_total), // Стоимость обучения прописью
            'cost_month_total_half' => $model->cost_month_total / 2,
            'cost_year_total' => $model->cost_year_total, // Полная стоимость обучения
            'cost_year_total_str' => PriceHelper::num2str($model->cost_year_total), // Полная стоимость обучения прописью
            'cost_year_grand_total' => $costYearGrandTotal, // Полная стоимость обучения
            'cost_year_grand_total_str' => PriceHelper::num2str($costYearGrandTotal), // Полная стоимость обучения прописью
            'cost_total_decline' => $costTotalDecline, // Снижение стоимости за счет средств гранта
            'cost_total_decline_str' => PriceHelper::num2str($costTotalDecline),
            'cost_grand_total' => $costYearGrandTotal + $costTotalDecline,
            'cost_grand_total_str' => PriceHelper::num2str($costYearGrandTotal + $costTotalDecline),
            'student_address' => $model->student->userAddress,
            'student_phone' => $model->student->userPhone,
            'student_gender' => $model->student->userGender == 1 ? 'он' : ($model->student->userGender == 2 ? 'она' : 'он(она)'),
            'student_sert_name' => Student::getDocumentValue($model->student->sert_name) ?: 'свидетельства о рождении',
            'student_sert_series' => $model->student->sert_series ?: '     ',
            'student_sert_num' => $model->student->sert_num ?: '      ',
            'student_sert_organ' => $model->student->sert_organ ?: '                                       ',
            'student_sert_date' => $model->student->sert_date ?: '             ',
            'student_snils' => $model->student->userSnils,
            'parent_address' => $model->parent->userAddress,
            'parent_phone' => $model->parent->userPhone,
            'parent_email' => $model->parent->userEmail,
            'parent_sert_name' => Parents::getDocumentValue($model->parent->sert_name) ?: 'паспорт',
            'parent_sert_series' => $model->parent->sert_series ?: '     ',
            'parent_sert_num' => $model->parent->sert_num ?: '      ',
            'parent_sert_code' => $model->parent->sert_code ?: '      ',
            'parent_sert_organ' => $model->parent->sert_organ ?: '                                       ',
            'parent_sert_date' => $model->parent->sert_date ?: '             ',
            'parent_sert_country' => $model->parent->sert_country ?: '             ',
            'parent_birth_date' => $model->parent->userBirthDate, // День рождения родителя
            'inn' => $invoices->inn,
            'kpp' => $invoices->kpp,
            'oktmo' => $invoices->oktmo,
            'recipient' => $invoices->recipient,
            'payment_account' => $invoices->payment_account,
            'personal_account' => $invoices->personal_account,
            'corr_account' => $invoices->corr_account,
            'bank_name' => $invoices->bank_name,
            'bik' => $invoices->bik,
            'kbk' => $invoices->kbk,
            'student_fls' => sprintf('%06d', $model->student_id),
            'plan_year' => $model->plan_year,
            'plan_year_next' => $model->plan_year + 1,
        ];

        $items = [];
        $schedule = [];
        $cost_year_total = $cost_month_total = $year_time_total = $week_time_total = 0;
        $models_sch = $this->getStudyplanSchedule();
        $models_sch = ArrayHelper::index($models_sch, null,['studyplan_subject_id']);
         foreach ($models_sch as $studyplan_subject_id => $d) {
             foreach ($d as $i => $m) {
                 $schedule[$studyplan_subject_id][] = \artsoft\helpers\ArtHelper::getWeekdayValue('short', $m['week_day']) . ' ' . $m['time_in'] . '-' . $m['time_out'];
             }
         }
        foreach ($modelsDependence as $item => $modelDep) {
            $items[] = [
                'rank' => 'dep',
                'item' => $item + 1,
                'subject_cat_name' => $modelDep->subjectCat->name,
                'subject_name_pur' => $modelDep->subject->name,
                'subject_name' => '(' . $modelDep->subject->name . ')',
                'subject_type_name' => $modelDep->subjectType->name,
                'subject_vid_name' => $modelDep->subjectVid->name,
                'week_time' => $modelDep->week_time,
                'year_time' => $modelDep->year_time,
                'cost_hour' => $modelDep->cost_hour,
                'cost_month_summ' => $modelDep->cost_month_summ,
                'cost_year_summ' => $modelDep->cost_year_summ,
                'year_time_consult' => $modelDep->year_time_consult,
                'schedule' => isset($schedule[$modelDep->id]) ? implode(', ', $schedule[$modelDep->id]) : 'не задано',
            ];
            $cost_year_total += $modelDep->cost_year_summ;
            $cost_month_total += $modelDep->cost_month_summ;
            $year_time_total += $modelDep->year_time;
            $week_time_total += $modelDep->week_time;
        }

//        echo '<pre>' . print_r(ArtHelper::getMonthsInRange($model->doc_contract_start, $model->doc_contract_end), true) . '</pre>'; die();
        $month_range = ArtHelper::getMonthsInRange($model->doc_contract_start, $model->doc_contract_end, "m.Y");

        $mStart = Yii::$app->formatter->asDate($model->doc_contract_start, 'php:m');
        $yStart = Yii::$app->formatter->asDate($model->doc_contract_start, 'php:Y');
        if ($mStart == 6) {
            unset($month_range['1']);
            unset($month_range['2']);
        } elseif ($mStart == 7) {
            unset($month_range['1']);
        }
        $month_range = array_values($month_range);
        $items_month = [];
        $discount_all = 0;
        foreach ($month_range as $item => $month) {
            $cost_m_total = $model->cost_month_total;
            if (in_array($mStart, [6, 7, 8]) && $item == 0) {
                $rem = '100% оплата за ' . $month_range[$item + 1];
            } elseif (in_array($mStart, [6, 7, 8]) && $item == 1) {
                $rem = '100% оплата за ' . $month_range[count($month_range) - 1];
            } elseif ($mStart == 9 && $item == 0) {
                $rem = '100% оплата за ' . $month_range[count($month_range) - 1] . ' + 100% оплата до начала занятий';
                $timestamp_in = \Yii::$app->formatter->asTimestamp(mktime(0, 0, 0, $mStart, 1, $yStart));
                $summ = (new Query())->from('activities_schedule_studyplan_view')
                    ->select(new \yii\db\Expression('SUM(datetime_out-datetime_in)'))
                    ->where(['between', 'datetime_in', $timestamp_in, Yii::$app->formatter->asTimestamp($model->doc_contract_start)])
                    ->andWhere(['direction_id' => 1000])
                    ->andWhere(['studyplan_id' => $this->id])
                    ->scalar();
                $discount = $model->year_time_total * Schedule::astr2academ($summ) != 0 ? round($model->cost_year_total / $model->year_time_total * Schedule::astr2academ($summ), 0) : 0;
                $discount_all += $discount;
                $cost_m_total = $model->cost_month_total * 2 - $discount;

            } else {
                $rem = '';
            }
            $items_month[$item] = [
                'rank' => 'm',
                'period' => $month,
                'cost_month_total_item' => $cost_m_total,
                'rem' => $rem,
            ];
        }

        if ($add_flag) {
            $data[0]['cost_month_total'] = $cost_month_total;
            $data[0]['cost_year_total_str'] = PriceHelper::num2str($cost_year_total);
            $data[0]['year_time_total'] = $year_time_total;
            $data[0]['cost_year_total'] = $cost_year_total;
            $data[0]['week_time_total'] = $week_time_total;
        } else {
            $cost_year_total = $model->cost_year_total - $discount_all;
            $data[0]['week_time_total'] = $week_time_total;
            if($cost_year_total != 0) {
                $data[0]['cost_year_total'] = $cost_year_total;
                $data[0]['cost_year_total_str'] = PriceHelper::num2str($cost_year_total);
            }
        }
        $month_total_item = $model->cost_month_total;
        $n = count($month_range);
        if (($month_total_item * $n) < $model->cost_year_total) {
            $month_total_item = $model->cost_year_total - ($month_total_item * $n) + $month_total_item;
        } else {
            $month_total_item = $month_total_item - (($month_total_item * $n) - $model->cost_year_total);
        }

        if ($month_total_item <= 0) {
            $items_month[0]['cost_month_total_item'] = $month_total_item + $model->cost_month_total;
            $items_month[$n - 1]['cost_month_total_item'] = 0;
        }
        if ($mStart == 9) {
            $items_month[$n - 1]['cost_month_total_item'] = 0;
        }

        $output_file_name = str_replace('.', '_' . $save_as . '_' . $model->doc_date . '.', basename($template));

        $tbs = DocTemplate::get($template)->setHandler(function ($tbs) use ($data, $items, $items_month) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('dep', $items);
            $tbs->MergeBlock('m', $items_month);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

    /**
     * @param $modelProgrammLevel
     */
    public function copyAttributes($modelProgrammLevel)
    {
        $this->year_time_total = $modelProgrammLevel->year_time_total;
        $this->cost_month_total = $modelProgrammLevel->cost_month_total;
        $this->cost_year_total = $modelProgrammLevel->cost_year_total;
    }

    /**
     * Расписание занятий плана учащегося
     *
     * @return array
     */
    public function getStudyplanSchedule()
    {
        $models = SubjectScheduleStudyplanView::find()
            ->where(['studyplan_id' => $this->id])
            ->andWhere(['not', ['subject_schedule_id' => null]])
            ->andWhere(['=', 'direction_id', 1000])
            ->all();

        $data = [];

        foreach ($models as $item => $modelSchedule) {
            $data[] = [
                'studyplan_subject_id' => $modelSchedule->studyplan_subject_id,
                'week_day' => $modelSchedule->week_day,
                'time_in' => $modelSchedule->time_in,
                'time_out' => $modelSchedule->time_out,
                'title' => $modelSchedule->sect_name . ' ' . RefBook::find('auditory_memo_1')->getValue($modelSchedule->auditory_id),
                'data' => [
                    'studyplan_id' => $this->id,
                    'schedule_id' => $modelSchedule->subject_schedule_id,
                    'teachers_load_id' => $modelSchedule->teachers_load_id,
                    'direction_id' => $modelSchedule->direction_id,
                    'teachers_id' => $modelSchedule->teachers_id,
                    'description' => $modelSchedule->description,
                    'week_num' => $modelSchedule->week_num,
                    'auditory_id' => $modelSchedule->auditory_id,
                    'style' => [
                        'background' => '#0000ff',
                        'color' => '#00ff00',
                        'border' => '#ff0000',
                    ]
                ]
            ];

        }
        return $data;
    }

    public static function getStudyplanSubjectListById($studyplan_id)
    {
        if (!$studyplan_id) return [];

        return Yii::$app->db->createCommand('SELECT distinct studyplan_subject_id as id, memo_1 as name
                                                    FROM studyplan_subject_view
                                                    WHERE studyplan_id=:studyplan_id ORDER BY memo_1',
            ['studyplan_id' => $studyplan_id,
            ])->queryAll();
    }

    public static function getStudyplanSubjectListByStudyplan($studyplan_id)
    {
        return ArrayHelper::map(self::getStudyplanSubjectListById($studyplan_id), 'id', 'name');
    }

    public static function getStudyplanSubjectListByStudyplanAndTeachersId($studyplan_id, $teachers_id)
    {
        if (!$studyplan_id || !$teachers_id) return [];
        return TeachersLoadStudyplanView::find()
            ->select('studyplan_subject_id as id, subject as name')
            ->distinct()
            ->where(['studyplan_id' => $studyplan_id])
            ->andWhere(['teachers_id' => $teachers_id])
            ->asArray()
            ->all();
    }

    public static function getStudyplanSubjectListByStudyplanAndTeachers($studyplan_id, $teachers_id)
    {
        return ArrayHelper::map(self::getStudyplanSubjectListByStudyplanAndTeachersId($studyplan_id, $teachers_id), 'id', 'name');
    }

    /**
     * backend/views/studyplan/default/load-items.php
     * @param $studyplan_id
     * @return array
     * @throws \yii\db\Exception
     */
//    public static function getSectListForStudyplan($studyplan_id)
//    {
//        $q = Yii::$app->db->createCommand('SELECT distinct subject_sect_studyplan_id
//	FROM teachers_load_studyplan_view where subject_sect_studyplan_id IS NOT NULL AND studyplan_id=:studyplan_id',
//            ['studyplan_id' => $studyplan_id,
//            ])->queryColumn();
//        $data = [];
//        foreach ($q as $item => $value) {
//            $data[$value] = $value !== 0 ? RefBook::find('sect_name_1')->getValue($value) : 'Индивидуально';
//        }
//
//        return $data;
//
//    }

    /**
     * Формируем инд. план на след год
     * @param int $next
     * @return bool
     */
    public function makeStadylan($next = 1)
    {
        ini_set('memory_limit', '128M');

        $plan_year = $this->plan_year + 1;
        $course = $this->course + $next;

        $exists = Studyplan::find()->where(['=', 'programm_id', $this->programm_id])
            ->andWhere(['=', 'plan_year', $plan_year])
            ->andWhere(['=', 'course', $course])
            ->andWhere(['=', 'student_id', $this->student_id])->exists();

        $modelProgrammLevel = EducationProgrammLevel::find()
            ->where(['programm_id' => $this->programm_id])
            ->andWhere(['course' => $course])
            ->one();
        if (!$exists && $modelProgrammLevel) {
            $transaction = \Yii::$app->db->beginTransaction();
            $model = new Studyplan();
            $model->setAttributes(
                [
                    'programm_id' => $this->programm_id,
                    'subject_form_id' => $this->subject_form_id,
                    'course' => $course,
                    'student_id' => $this->student_id,
                    'plan_year' => $plan_year,
                ]
            );
            try {
                $model->copyAttributes($modelProgrammLevel);

                if ($flag = $model->save(false)) {

                    if (isset($modelProgrammLevel->educationProgrammLevelSubject)) {
                        $modelsSubTime = $modelProgrammLevel->educationProgrammLevelSubject;
                        foreach ($modelsSubTime as $modelSubTime) {
                            $modelSub = new StudyplanSubject();

                            $modelSub->copyAttributes($model, $modelSubTime);
                            if (!$modelSub->subject_id) {
                                $m = $this->getStudyplanSubject()->select('subject_id, subject_vid_id')->andWhere(['=', 'subject_cat_id', $modelSub->subject_cat_id])->one();
                                $modelSub->subject_id = $m->subject_id ?? null;
                                $modelSub->subject_vid_id = $m->subject_vid_id ?? null;
//echo '<pre>' . print_r($m, true) . '</pre>'; die();
                            }
                            if (!($flag = $modelSub->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                }

                if ($flag) {
                    $this->makeStadylanLoad($next, $model);
                    $transaction->commit();
                    return true;
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                echo '<pre>' . print_r($e->getMessage(), true) . '</pre>';
                return false;
            }
        }

    }

    public function makeStadylanLoad($next, $newModel)
    {
        try {
            $data = TeachersLoadStudyplanView::find()
                ->where(['studyplan_id' => $this->id])
                ->andWhere(['=', 'plan_year', $this->plan_year])
                ->andWhere(['IS NOT', 'subject_sect_studyplan_id', NULL])
                ->asArray()
                ->all();
//        echo '<pre>' . print_r($data, true) . '</pre>';die();
            foreach ($data as $modelSub) {
                if (!$modelSub['teachers_id']) {
                    continue;
                }
                $model_subject = StudyplanSubject::find()
                    ->where(['=', 'studyplan_id', $newModel->id])
                    ->andWhere(['=', 'subject_type_id', $modelSub['subject_type_id']])
                    ->andWhere(['=', 'subject_vid_id', $modelSub['subject_vid_id']])
                    ->andWhere(['=', 'subject_cat_id', $modelSub['subject_cat_id']])
                    ->andWhere(['=', 'subject_id', $modelSub['subject_id']])
                    ->one();
                // echo '<pre>' . print_r($model_subject, true) . '</pre>';die();
                if ($modelSub['subject_sect_studyplan_id'] == 0) {
                    if ($model_subject) {
                        $model_load = TeachersLoad::find()
                                ->where(['=', 'studyplan_subject_id', $model_subject->id])
                                ->andWhere(['=', 'subject_sect_studyplan_id', 0])
                                ->andWhere(['=', 'direction_id', $modelSub['direction_id']])
                                ->andWhere(['=', 'direction_vid_id', $modelSub['direction_vid_id']])
                                ->andWhere(['=', 'teachers_id', $modelSub['teachers_id']])
                                ->one() ?? new TeachersLoad();
                        $model_load->subject_sect_studyplan_id = 0;
                        $model_load->studyplan_subject_id = $model_subject->id;
                        $model_load->direction_id = $modelSub['direction_id'] ?? null;
                        $model_load->direction_vid_id = $modelSub['direction_vid_id'] ?? null;
                        $model_load->teachers_id = $modelSub['teachers_id'] ?? null;
                        $model_load->load_time = $this->getLoadTime($model_load->direction_id, $model_subject->week_time);
                        $model_load->load_time_consult = $model_load->direction_id == 1000 ? $model_subject->year_time_consult : 0;
//            echo '<pre>' . print_r($model_load, true) . '</pre>';die();
                        $model_load->save(false);
                    }
                } else {
                    $modelSect = SubjectSect::find()
                        ->where(['=', 'id', $modelSub['subject_sect_id']])
                        ->andWhere(['OR', new \yii\db\Expression(":course = any (string_to_array(course_list, ',')::int[])", [':course' => $this->course + $next]), ['IS', 'course_list', NULL]])// есть ли ограничения по классам?
                        ->one();
                    if ($modelSect) {
                        $modelSect->setSubjectSect($newModel->plan_year);

                        $modelSectStudyplan = SubjectSectStudyplan::find()
                            ->where(['=', 'id', $modelSub['subject_sect_studyplan_id']])
                            ->one();
                        if ($modelSectStudyplan) {
                            $course = $modelSectStudyplan->course != null ? $modelSectStudyplan->course + $next : null;
//                        echo '<pre>' . print_r([$newModel->plan_year, $course, $modelSectStudyplan->group_num], true) . '</pre>'; die();
                            $model_sect = SubjectSectStudyplan::find()
                                ->where(['=', 'subject_sect_id', $modelSub['subject_sect_id']])
                                ->andWhere(['=', 'plan_year', $newModel->plan_year]);
                            if ($course != null) {
                                $model_sect = $model_sect->andWhere(['=', 'course', $course]);
                            }
                            if ($modelSectStudyplan->group_num != null) {
                                $model_sect = $model_sect->andWhere(['=', 'group_num', $modelSectStudyplan->group_num]);
                            }
                            $model_sect = $model_sect->one();

                            // echo '<pre>' . print_r($model_sect, true) . '</pre>'; die();
                            if ($model_sect && $model_subject) {
                                $model_sect->insertStudyplanSubject($model_subject->id);
                                $model_load = TeachersLoad::find()
                                        ->where(['=', 'studyplan_subject_id', 0])
                                        ->andWhere(['=', 'subject_sect_studyplan_id', $model_sect->id])
                                        ->andWhere(['=', 'direction_id', $modelSub['direction_id']])
                                        ->andWhere(['=', 'direction_vid_id', $modelSub['direction_vid_id']])
                                        ->andWhere(['=', 'teachers_id', $modelSub['teachers_id']])
                                        ->one() ?? new TeachersLoad();
                                $model_load->subject_sect_studyplan_id = $model_sect->id;
                                $model_load->studyplan_subject_id = 0;
                                $model_load->direction_id = $modelSub['direction_id'] ?? null;
                                $model_load->direction_vid_id = $modelSub['direction_vid_id'] ?? null;
                                $model_load->teachers_id = $modelSub['teachers_id'] ?? null;
                                $model_load->load_time = $model_subject->week_time;
                                $model_load->load_time_consult = $model_subject->year_time_consult;
                                $model_load->save(false);
                            }
                        }
                    }
//                echo '<pre>' . print_r($data, true) . '</pre>';die();
                }
            }
        } catch (Exception $e) {
            echo '<pre>' . print_r($e->getMessage(), true) . '</pre>';
            return false;
        }
    }

    protected function getLoadTime($direction_id, $week_time)
    {
        if ($direction_id == 1000) {
            return $week_time;
        }
        switch ($week_time) {
            case 1:
            case 2:
                return 1;
                break;
            case 2.5:
                return 1.5;
                break;
            default:
                return $week_time;
        }
    }

    /**
     * @param int $next
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteStadylan($next = 1)
    {
        $plan_year = $this->plan_year + 1;
        $course = $this->course + $next;

        $model = Studyplan::find()->where(['=', 'programm_id', $this->programm_id])
            ->andWhere(['=', 'plan_year', $plan_year])
            ->andWhere(['=', 'course', $course])
            ->andWhere(['=', 'student_id', $this->student_id])->one();
//        echo '<pre>' . print_r($model, true) . '</pre>';die();
        if ($model) {
            $model->delete(false);
        }
    }

    public static function getStudentStudyplanDefault($student_id)
    {
        $plan_year = ArtHelper::getStudyYearDefault(6);
        return self::find()->where(['=', 'student_id', $student_id])
                ->andWhere(['=', 'plan_year', $plan_year])
                ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])
                ->scalar() ?? null;

    }

    public static function getParentStudyplanDefault($parents_id)
    {
        $plan_year = ArtHelper::getStudyYearDefault(6);
        return self::find()
                ->innerJoin('student_dependence', 'studyplan.student_id=student_dependence.student_id')
                ->where(['=', 'student_dependence.parent_id', $parents_id])
                ->andWhere(['=', 'plan_year', $plan_year])
                ->andWhere(['=', 'studyplan.status', Studyplan::STATUS_ACTIVE])
                ->scalar() ?? null;

    }

    public static function getStudentListForPlanYear($plan_year)
    {
        return \yii\helpers\ArrayHelper::map((new Query())->select('student_id as id, student_fio as name')
            ->distinct()
            ->from('studyplan_view')
            ->where(['=', 'plan_year', $plan_year])
            ->orderBy('student_fio')
            ->all(), 'id', 'name');
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeSave($insert)
    {
        if ($this->status == self::STATUS_ACTIVE) {
            if ($this->status_reason == 1) {
                $this->deleteStadylan();
            } elseif ($this->status_reason == 2) {
                $this->deleteStadylan(0);
            }
            $this->status_reason = 0;
        } else {
            $this->cond_flag = false;
            if ($this->status_reason == 1) {
                $this->makeStadylan();
            } elseif ($this->status_reason == 2) {
                $this->makeStadylan(0);
            }
        }
        return parent::beforeSave($insert);
    }

    public static function find()
    {
        return new StudyplanQuery(get_called_class());
    }

    /**
     * Проверка готовы ли документы к печати
     * @return bool
     */
    public function docWaitingPrint()
    {
        if ($this->doc_contract_start && $this->doc_contract_end && $this->doc_signer) {
            return true;
        }
        return false;
    }

    /**
     * Определяем, есть ли в плане бюджетника внебюджетные дисциплины для доп договора
     * @return bool
     */
    public function isAdditionalContract()
    {
        if ($this->subject_form_id != 1001) {
            if ($this->getStudyplanSubject()->andWhere(['subject_type_id' => 1001])->exists()) {
                return true;
            }
        }
        return false;
    }
}
