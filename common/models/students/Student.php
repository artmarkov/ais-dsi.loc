<?php

namespace common\models\students;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\db\ActiveRecord;
use common\models\studyplan\Studyplan;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * This is the model class for table "students".
 *
 * @property int $id
 * @property int $user_common_id
 * @property string $limited_status_list
 * @property string $sert_name
 * @property string $sert_series
 * @property string $sert_num
 * @property string $sert_organ
 * @property string $sert_date
 *
 * @property UserCommon $user
 * @property Studyplan $studyplans
 * @property StudentDependence $studentDependence
 */
class Student extends ActiveRecord
{
    const STUDENT_DOC = [
        'password' => 'Паспорт',
        'birth_cert' => 'Свидетельство о рождении',
        'birth_cert_int' => 'Свидетельство о рождении иностранного образца',
        'birth_cert_egr' => 'Запись о рождении ЕГР ЗАГС',
    ];

    const LIMITED_STATUS = [
        1000 => 'Ребенок-инвалид',
        2000 => 'Ребенок с ОВЗ',
        3000 => 'Ребенок под опекой',
        4000 => 'Ребенок из многодетной семьи',
        5000 => 'СВО',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'students';
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
                'class' => DateFieldBehavior::class,
                'attributes' => ['sert_date'],
            ],
            [
                'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
            ],
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['limited_status_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id'], 'required'],
            [['sert_date'], 'safe'],
            [['limited_status_list'], 'safe'],
            [['sert_name', 'sert_series', 'sert_num'], 'string', 'max' => 32],
            [['sert_organ'], 'string', 'max' => 127],
            // при заполнении одного из полей, делаем обязательными остальные поля блока документа
            [['sert_num', 'sert_organ',  'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_series != NULL;
            }, 'whenClient' => "function (attribute, value) {
                        return $('#student-sert_series').val() != NULL;
                    }"],
            [['sert_date', 'sert_organ'], 'required', 'when' => function ($model) {
                return $model->sert_num != NULL;
            }, 'whenClient' => "function (attribute, value) {
                        return $('#student-sert_num').val() != NULL;
                    }"],
            [['sert_num', 'sert_date'], 'required', 'when' => function ($model) {
                return $model->sert_organ != NULL;
            }, 'whenClient' => "function (attribute, value) {
                        return $('#student-sert_organ').val() != NULL;
                    }"],
            [['sert_num', 'sert_organ'], 'required', 'when' => function ($model) {
                return $model->sert_date != NULL;
            }, 'whenClient' => "function (attribute, value) {
                        return $('#student-sert_date').val() != NULL;
                    }"],
            ['sert_date', 'default', 'value' => NULL],
            [['user_common_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['user_common_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/student', 'ID'),
            'sert_name' => Yii::t('art/student', 'Sertificate Name'),
            'sert_series' => Yii::t('art/student', 'Sertificate Series'),
            'sert_num' => Yii::t('art/student', 'Sertificate Num'),
            'sert_organ' => Yii::t('art/student', 'Sertificate Organ'),
            'sert_date' => Yii::t('art/student', 'Sertificate Date'),
            'fullName' => Yii::t('art', 'Full Name'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'userStatus' => Yii::t('art', 'Status'),
            'limited_status_list' => Yii::t('art/student', 'Limited status list'),
            'first_name' => Yii::t('art', 'First Name'),
            'middle_name' => Yii::t('art', 'Middle Name'),
            'last_name' => Yii::t('art', 'Last Name'),

        ];
    }

    public function optimisticLock()
    {
        return 'version';
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
        return $this->user ? $this->user->status : null;
    }

    public function getUserBirthDate()
    {
        return $this->user ? $this->user->birth_date : null;
    }

    public function getUserPhone()
    {
        return $this->user ? ($this->user->phone ? $this->user->phone : $this->user->phone_optional) : null;
    }

    public function getUserAddress()
    {
        return $this->user ? $this->user->address : null;
    }

    public function getUserEmail()
    {
        return $this->user ? $this->user->email : null;
    }

    public function getUserSnils()
    {
        return $this->user ? $this->user->snils : null;
    }

    public static function getDocumentValue($val)
    {
        $ar = self::STUDENT_DOC;

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * Геттер полного имени юзера
     */
    public function getFullName()
    {
        return $this->user ? $this->user->fullName : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getStudentDependence()
    {
        return $this->hasMany(StudentDependence::class, ['student_id' => 'id']);
    }

    public function getStudyplans()
    {
        return $this->hasMany(Studyplan::class, ['student_id' => 'id']);
    }

    public function getStudentDependenceNameById($student_id)
    {
        return StudentDependence::find(['student_id' => $student_id])
            ->innerJoin('userRelation');
    }

    public static function getLimitedStatusList()
    {
        return self::LIMITED_STATUS;
    }

    public static function getLimitedStatusValue($val)
    {
        $ar = self::getLimitedStatusList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        if ($this->studyplans) {
            return false;
        }
        foreach ($this->studentDependence as $model) {
            if (!$model->delete(false)) {
                break;
                return false;
            }
        }
        $model = $this->user;
        if ($model) {
            return $model->delete(false);
        }

        return parent::beforeDelete();
    }

    public static function getStudentList()
    {
        $query = (new Query())->from('students_view')
            ->select(['students_id', 'CONCAT(fullname, \' - \', to_char(to_timestamp(birth_date + 10800), \'DD.MM.YYYY\'), \' (\', birth_date_age, \')\') as fio'])
            ->distinct()
            ->all();
        return \yii\helpers\ArrayHelper::map($query, 'students_id', 'fio');

    }
}
