<?php

namespace common\models\studyplan;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\PriceHelper;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\education\EducationProgrammLevel;
use common\models\guidejob\Direction;
use common\models\own\Invoices;
use common\models\parents\Parents;
use common\models\students\Student;
use common\models\subject\SubjectType;
use common\models\teachers\Teachers;
use common\widgets\qrcode\QRcode;
use common\widgets\qrcode\widgets\Text;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;
use function morphos\Russian\inflectName;

/**
 * This is the model class for table "studyplan_invoices".
 *
 * @property int $id
 * @property int $studyplan_id Учебный план
 * @property int $invoices_id Вид платежа
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property int|null $type_id Тип платежа(бюджет,внебюджет)
 * @property int|null $vid_id Вид платежа(индивидуальные, групповые)
 * @property int|null $month_time_fact Фактически оплаченные часы
 * @property int|null $invoices_tabel_flag Учесть в табеле фактически оплаченные часы
 * @property int|null $invoices_date Дата платежа
 * @property float|null $invoices_summ Сумма платежа
 * @property int|null $payment_time Время выполнения платежя
 * @property int|null $payment_time_fact Время поступления денег на счет
 * @property string|null $invoices_app Назначение платежа
 * @property string|null $invoices_rem Примечание к платежу
 * @property int $status Статус платежа(В работе,Оплачено,Задолженность по оплате)
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property SubjectType $subjectType
 * @property Direction $direction
 * @property Invoices $invoices
 * @property Studyplan $studyplan
 * @property Teachers $teachers
 */
class StudyplanInvoices extends \artsoft\db\ActiveRecord
{
    const STATUS_WORK = 2;
    const STATUS_PAYD = 3;
    const STATUS_ARREARS = 4;
    const STATUS_RECEIPT = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_invoices';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studyplan_id', 'invoices_id', 'type_id', 'invoices_summ', 'invoices_app'], 'required'],
            [['studyplan_id', 'invoices_id', 'direction_id', 'teachers_id', 'type_id', 'vid_id', 'month_time_fact', 'invoices_tabel_flag', 'status'], 'integer'],
            [['invoices_date', 'payment_time', 'payment_time_fact'], 'safe'],
            [['invoices_summ'], 'number'],
            ['status', 'default', 'value' => function () {
                return self::STATUS_WORK;
            }],
            ['invoices_date', 'default', 'value' => function () {
                return Schedule::getStartEndDay()[0];
            }],
            ['payment_time', 'default', 'value' => function () {
                return Schedule::getStartEndDay()[0];
            }, 'when' => function () {
                return $this->status == self::STATUS_PAYD;
            }],
            ['payment_time_fact', 'default', 'value' => function () {
                return Schedule::getStartEndDay()[0];
            }, 'when' => function () {
                return $this->status == self::STATUS_RECEIPT;
            }],
            [['invoices_app'], 'string', 'max' => 256],
            [['invoices_rem'], 'string', 'max' => 512],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::class, 'targetAttribute' => ['type_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['invoices_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoices::class, 'targetAttribute' => ['invoices_id' => 'id']],
            [['studyplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Studyplan::class, 'targetAttribute' => ['studyplan_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'studyplan_id' => 'Учебный план',
            'invoices_id' => 'Вид платежа',
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teacher'),
            'type_id' => 'Тип платежа',
            'vid_id' => 'Вид платежа',
            'month_time_fact' => 'Фактически оплаченные часы',
            'invoices_tabel_flag' => 'Учесть в табеле',
            'invoices_date' => 'Дата платежа',
            'invoices_summ' => 'Сумма платежа',
            'payment_time' => 'Время выполнения платежя',
            'payment_time_fact' => 'Время поступления денег на счет',
            'invoices_app' => 'Назначение платежа',
            'invoices_rem' => 'Примечание к платежу',
            'status' => 'Статус платежа',
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
     * Gets query for [[SubjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::class, ['id' => 'type_id']);
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
     * Gets query for [[Invoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasOne(Invoices::class, ['id' => 'invoices_id']);
    }

    /**
     * Gets query for [[Studyplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplan()
    {
        return $this->hasOne(Studyplan::class, ['id' => 'studyplan_id']);
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

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_WORK => 'Счет в работе',
            self::STATUS_PAYD => 'Счет оплачен',
            self::STATUS_ARREARS => 'Задолженность по оплате',
            self::STATUS_RECEIPT => 'Поступили средства',
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
     * формирование квитанции
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function makeDocx()
    {
        $template = 'document/invoices_pd4.docx';
        $model = $this;
        $invoices = $model->invoices;
        $studyplan = $model->studyplan;
        $teachers = $model->teachers;
        $save_as = str_replace(' ', '_', $model->studyplan_id);

        $data[] = [
            'rank' => 'doc',
            'invoices_date' => date('j', $model->invoices_date) . ' ' . ArtHelper::getMonthsList()[date('n', $model->invoices_date)] . ' ' . date('Y', $model->invoices_date), // дата платежа
            'invoices_summ' => $model->invoices_summ . ' руб.',
            'invoices_app' => $model->invoices_app,
            'student' => $studyplan->student->getFullName(), // Полное имя ученика
            'student_address' => $studyplan->student->getUserAddress(),
            'student_fls' => sprintf('%06d', $model->studyplan_id),
            'recipient' => $invoices->recipient,
            'inn' => $invoices->inn,
            'payment_account' => $invoices->payment_account,
            'kpp' => $invoices->kpp,
            'personal_account' => $invoices->personal_account,
            'bank_name' => $invoices->bank_name,
            'bik' => $invoices->bik,
            'kbk' => $invoices->kbk,
            'class_teacher_info' => RefBook::find('education_programm_short_name')->getValue($studyplan->programm_id) . ' ' . $studyplan->course . ' класс ' . isset($model->teachers_id) ? RefBook::find('teachers_fio')->getValue($model->teachers_id) : '',
            'qr_code' => Text::widget([
                'outputDir' => '@webroot/upload/qrcode',
                'outputDirWeb' => '@web/upload/qrcode',
                'ecLevel' => QRcode::QR_ECLEVEL_L,
                'text' => 'ST00012|Name=ГБУДО г. Москвы "ДШИ им.И.Ф.Стравинского")|PersonalAcc=03224643450000007300|BankName=ГУ Банка России по ЦФО//УФК по г.Москве г.Москва|BIC=004525988|CorrespAcc=40102810545370000003|Sum=340000|Purpose= Оплата за март 2021 г.ВН|PayeeINN=7733098705|KPP=773301001|CBC=05600000000131131022|OKATO=|lastName=Туйцына|firstName=Анастасия|middleName=Евгеньевна|persAcc=03992|childFio=Туйцына Анастасия Евгеньевна|paymPeriod=Март, 2021 г.|instNum=ДШИ им.И.Ф.Стравинского|classNum=Веселые нотки',
                'size' => 2,
            ]),
        ];

        $output_file_name = str_replace('.', '_' . $save_as . '_' . Yii::$app->formatter->asDate($model->invoices_date, 'php:Y_m_d') . '.', basename($template));

        $tbs = DocTemplate::get($template)->setHandler(function ($tbs) use ($data) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}
