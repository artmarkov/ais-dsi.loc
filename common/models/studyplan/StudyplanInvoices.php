<?php

namespace common\models\studyplan;

use artsoft\behaviors\DateFieldBehavior;
use common\models\guidejob\Direction;
use common\models\own\Invoices;
use common\models\subject\SubjectType;
use common\models\teachers\Teachers;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

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
//            [
//                'class' => DateFieldBehavior::class,
//                'attributes' => ['invoices_date', 'payment_time', 'payment_time_fact'],
//            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studyplan_id', 'invoices_id', 'type_id'], 'required'],
            [['studyplan_id', 'invoices_id', 'direction_id', 'teachers_id', 'type_id', 'vid_id', 'month_time_fact', 'invoices_tabel_flag',  'status'], 'integer'],
            [['invoices_date', 'payment_time', 'payment_time_fact'], 'safe'],
            [['invoices_summ'], 'number'],
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
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
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
    
}
