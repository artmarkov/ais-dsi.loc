<?php

namespace common\models\studyplan;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\PriceHelper;
use artsoft\helpers\RefBook;
use common\models\education\EducationProgramm;
use common\models\education\EducationProgrammLevel;
use common\models\parents\Parents;
use common\models\students\Student;
use common\models\subject\Subject;
use common\models\schedule\SubjectScheduleStudyplanView;
use common\models\subject\SubjectType;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

use yii\helpers\ArrayHelper;
use function morphos\Russian\inflectName;

/**
 * This is the model class for table "studyplan".
 *
 * @property int $id
 * @property int $student_id
 * @property int $programm_id
 * @property int $subject_type_id
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
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property EducationProgramm $programm
 * @property Student $student
 */
class Studyplan extends \artsoft\db\ActiveRecord
{
// Шаблоны документов
    const template_csf = 'document/contract_student_free.docx';
    const template_cs = 'document/contract_student.docx';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'programm_id', 'subject_type_id', 'course', 'plan_year'], 'required'],
//            [['doc_date', 'doc_contract_start', 'doc_contract_end', 'doc_signer'], 'required', 'when' => function ($model) {
//                return !$model->isNewRecord;
//            }],
            [['student_id', 'programm_id',  'course', 'plan_year', 'subject_type_id', 'status', 'version'], 'integer'],
            [['doc_signer', 'doc_received_flag', 'doc_sent_flag'], 'integer'],
            [['doc_date', 'doc_contract_start', 'doc_contract_end'], 'safe'],
            ['doc_date', 'default', 'value' => date('d.m.Y')],
            [['description'], 'string', 'max' => 1024],
            [['year_time_total', 'cost_month_total', 'cost_year_total'], 'number'],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['programm_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::class, 'targetAttribute' => ['subject_type_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['doc_signer'], 'exist', 'skipOnError' => true, 'targetClass' => Parents::class, 'targetAttribute' => ['doc_signer' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/studyplan', 'ID'),
            'student_id' => Yii::t('art/student', 'Student'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'programmName' => Yii::t('art/studyplan', 'Education Programm'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
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
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
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

    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::class, ['id' => 'subject_type_id']);
    }

    /**
     * Геттер категория
     * @return array
     */
    public function getType()
    {
        return $this->subject_type_id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanSubject()
    {
        return $this->hasMany(StudyplanSubject::class, ['studyplan_id' => 'id'])->innerJoin('guide_subject_category', 'guide_subject_category.id = studyplan_subject.subject_cat_id')->orderBy('sort_order, subject_vid_id');
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
            $data = $data->indexBy('id')->column();
        }
        return $data;
    }


    /**
     * формирование документов: Согласие на обработку пд и Договор об оказании услуг
     *
     * @param $template
     * @throws \yii\base\InvalidConfigException
     */
    public function makeDocx($template)
    {
        $model = $this;
        $modelsDependence = $model->studyplanSubject;
        $modelProgrammLevel = EducationProgrammLevel::find()
            ->where(['programm_id' => $model->programm_id])
            ->andWhere(['course' => $model->course])
            ->one();

        $save_as = str_replace(' ', '_', $model->student->fullName);
        $data[] = [
            'rank' => 'doc',
            'doc_date' => date('j', strtotime($model->doc_date)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_date))] . ' ' . date('Y', strtotime($model->doc_date)), // дата договора
            'doc_signer' => $model->parent->fullName, // Полное имя подписанта-родителя
            'doc_signer_iof' => RefBook::find('parents_iof')->getValue($model->parent->id),
            'doc_signer_gen' => inflectName($model->parent->fullName, 'родительный'), // Полное имя подписанта-родителя родительный
            'doc_signer_dat' => inflectName($model->parent->fullName, 'дательный'), // Полное имя подписанта-родителя дательный
            'doc_student' => $model->student->fullName, // Полное имя ученика
            'doc_student_gen' => inflectName($model->student->fullName, 'родительный'), // Полное имя ученика родительный
            'doc_student_acc' => inflectName($model->student->fullName, 'винительный'), // Полное имя ученика винительный
            'student_birth_date' => $model->student->userBirthDate, // День рождения ученика
            'student_relation' => mb_strtolower(RefBook::find('parents_dependence_relation_name', $model->student_id)->getValue($model->parent->id), 'UTF-8'),
            'doc_contract_start' => date('j', strtotime($model->doc_contract_start)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_contract_start))] . ' ' . date('Y', strtotime($model->doc_contract_start)), // дата начала договора
            'doc_contract_end' => date('j', strtotime($model->doc_contract_end)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_contract_end))] . ' ' . date('Y', strtotime($model->doc_contract_end)), $model->doc_contract_end, // Дата окончания договора
            'programm_name' => $model->programm->name, // название программы
            'programm_level' => isset($modelProgrammLevel->level) ? $modelProgrammLevel->level->name : null, // уровень программы
            'term_mastering' => 'Срок обучения:' . $model->programm->term_mastering, // Срок освоения образовательной программы
            'course' => $model->course . ' класс',
            'year_time_total' => $model->year_time_total,
            'cost_month_total' => $model->cost_month_total,
            'cost_year_total' => $model->cost_year_total, // Полная стоимость обучения
            'cost_year_total_str' => PriceHelper::num2str($model->cost_year_total), // Полная стоимость обучения прописью
            'student_address' => $model->student->userAddress,
            'student_phone' => $model->student->userPhone,
            'student_sert_name' => Student::getDocumentValue($model->student->sert_name),
            'student_sert_series' => $model->student->sert_series,
            'student_sert_num' => $model->student->sert_num,
            'student_sert_organ' => $model->student->sert_organ,
            'student_sert_date' => $model->student->sert_date,
            'parent_address' => $model->parent->userAddress,
            'parent_phone' => $model->parent->userPhone,
            'parent_sert_name' => Parents::getDocumentValue($model->parent->sert_name),
            'parent_sert_series' => $model->parent->sert_series,
            'parent_sert_num' => $model->parent->sert_num,
            'parent_sert_organ' => $model->parent->sert_organ,
            'parent_sert_date' => $model->parent->sert_date,

        ];
        $items = [];
        foreach ($modelsDependence as $item => $modelDep) {
            $items[] = [
                'rank' => 'dep',
                'item' => $item + 1,
                'subject_cat_name' => $modelDep->subjectCat->name,
                'subject_name' => '(' . $modelDep->subject->name . ')',
                'subject_type_name' => $modelDep->subjectType->name,
                'subject_vid_name' => $modelDep->subjectVid->name,
                'week_time' => $modelDep->week_time,
                'year_time' => $modelDep->year_time,
                'cost_hour' => $modelDep->cost_hour,
                'cost_month_summ' => $modelDep->cost_month_summ,
                'cost_year_summ' => $modelDep->cost_year_summ,
                'year_time_consult' => $modelDep->year_time_consult,
            ];
        }
        $output_file_name = str_replace('.', '_' . $save_as . '_' . $model->doc_date . '.', basename($template));

        $tbs = DocTemplate::get($template)->setHandler(function ($tbs) use ($data, $items) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('dep', $items);

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
                    'week_day' => $modelSchedule->week_day,
                    'time_in' => $modelSchedule->time_in,
                    'time_out' => $modelSchedule->time_out,
                    'title' => $modelSchedule->sect_name,
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

    /**
     * backend/views/studyplan/default/load-items.php
     * @param $studyplan_id
     * @return array
     * @throws \yii\db\Exception
     */
//    public static function getSubjectListForStudyplan($studyplan_id)
//    {
//        return ArrayHelper::map(Yii::$app->db->createCommand('SELECT distinct studyplan_subject_id, memo_1
//                                                    FROM studyplan_subject_view
//                                                    WHERE studyplan_id=:studyplan_id ORDER BY memo_1',
//            ['studyplan_id' => $studyplan_id,
//            ])->queryAll(), 'studyplan_subject_id', 'memo_1');
//    }

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
}
