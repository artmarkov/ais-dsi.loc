<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use artsoft\helpers\Html;
use artsoft\models\User;
use common\models\activities\ActivitiesOver;
use common\models\auditory\Auditory;
use common\models\efficiency\TeachersEfficiency;
use common\models\guidesys\GuidePlanTree;
use common\models\teachers\TeachersLoadStudyplanView;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "schoolplan".
 *
 * @property int $id
 * @property int $author_id Автор записи
 * @property int $signer_id Подписант
 * @property string|null $title Название мероприятия
 * @property int $datetime_in Дата и время начала
 * @property int $datetime_out Дата и время окончания
 * @property string|null $places Место проведения
 * @property int|null $auditory_id Аудитория
 * @property string|null $department_list Отделы
 * @property string|null $executors_list Ответственные
 * @property int $category_id Категория мероприятия
 * @property int $activities_over_id ИД мероприятия вне плана (подготовка к мероприятию)
 * @property int|null $form_partic Форма участия
 * @property string|null $partic_price Стоимость участия
 * @property int|null $visit_poss Возможность посещения
 * @property string|null $visit_content Комментарий по посещению
 * @property int|null $important_event Значимость мероприятия
 * @property int|null $format_event Формат мероприятия
 * @property string|null $region_partners Зарубежные и региональные партнеры
 * @property string|null $site_url Ссылка на мероприятие (сайт/соцсети)
 * @property string|null $site_media Ссылка на медиаресурс
 * @property string|null $description Описание мероприятия
 * @property string|null $rider Технические требования
 * @property string|null $result Итоги мероприятия
 * @property int|null $num_users Количество участников
 * @property int|null $num_winners Количество победителей
 * @property int|null $num_visitors Количество зрителей
 * @property int $bars_flag Отправлено в БАРС
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 * @property int $doc_status
 * 
 * @property int $period_over Период подготовки перед мероприятием мин.
 * @property int $period_over_flag
 * @property int $executor_over_id Ответственный за подготовку
 * @property string $title_over Примечание
 * 
 * @property int $protocol_leader_id Реководитель комиссии user_id
 * @property int $protocol_secretary_id Секретарь комиссии user_id
 * @property string $protocol_members_list Члены комиссии user_id
 * @property string $protocol_subject_list Дисциплины
 * @property string $protocol_class_list Классы
 * 
 * @property Auditory $auditory
 * @property Author $author
 * @property User $user
 * @property GuidePlanTree $category
 * @property ActivitiesOver $activitiesOver
 * @property TeachersEfficiency $teachersEfficiency
 * @property SchoolplanPerform $schoolplanPerform
 */
class Schoolplan extends \artsoft\db\ActiveRecord
{
    public $period_over;
    public $period_over_flag;
    public $executor_over_id;
    public $title_over;

    public $admin_message;
    public $admin_flag;
    public $formPlaces;

    const FORM_PARTIC = [
        1 => 'Беcплатное',
        2 => 'Платное',
    ];

    const FORM_PLACES = [
        1 => 'Внутреннее',
        2 => 'Внешнее',
    ];

    const PERIOD_OVER = [
        30 => '30 мин',
        60 => '60 мин',
        90 => '90 мин',
        120 => '120 мин'];

    const VISIT_POSS = [
        '1' => 'Открытое',
        '2' => 'Закрытое',
    ];

    const IMPORTANT = [
        '1' => 'Обычное',
        '2' => 'Значимое',
    ];
    const FORMAT = [
        '1' => 'Очный формат',
        '2' => 'Дистанционный on-line формат',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan';
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
                'attributes' => ['datetime_in', 'datetime_out'],
                'timeFormat' => 'd.m.Y H:i'
            ],
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['department_list', 'executors_list', 'protocol_members_list','protocol_subject_list','protocol_class_list'],
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
            [['title', 'datetime_in', 'datetime_out', 'category_id', 'author_id', 'signer_id'], 'required'],
            [['department_list', 'executors_list'], 'required'],
            [['partic_price'], 'required', 'when' => function ($model) {
                return $model->form_partic == '2';
            }, 'enableClientValidation' => false],
            [['department_list', 'executors_list', 'datetime_in', 'datetime_out'], 'safe'],
            [['auditory_id', 'category_id', 'activities_over_id', 'form_partic', 'visit_poss', 'important_event', 'format_event', 'num_users', 'num_winners', 'num_visitors', 'author_id', 'signer_id'], 'integer'],
            [['visit_content', 'region_partners', 'rider', 'result'], 'string'],
            [['site_url', 'site_media'], 'url', 'defaultScheme' => 'http'],
            [['title'], 'string', 'max' => 512],
            [['places'], 'string', 'max' => 512],
            [['description'], 'default', 'value' => null],
            [['doc_status'], 'default', 'value' => 0],
            [['partic_price', 'site_url', 'site_media'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 4000, 'min' => 1000, 'when' => function ($model) {
                return $model->category->description_flag && !$model->isNewRecord;
            }, 'enableClientValidation' => false, 'skipOnEmpty' => false, 'message' => 'Введите описание мероприятия минимум 1000 знаков, включая пробелы.'],
            ['description', 'string', 'max' => 4000, 'when' => function ($model) {
                return !$model->category->description_flag && !$model->isNewRecord;
            }, 'enableClientValidation' => false, 'message' => 'Введите описание мероприятия минимум 500 знаков, включая пробелы.'],
            [['auditory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditory::class, 'targetAttribute' => ['auditory_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuidePlanTree::class, 'targetAttribute' => ['category_id' => 'id']],
            [['activities_over_id'], 'exist', 'skipOnError' => true, 'targetClass' => ActivitiesOver::class, 'targetAttribute' => ['activities_over_id' => 'id']],
            [['datetime_in', 'datetime_out'], 'checkFormatDateTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['datetime_out'], 'compareTimestamp', 'skipOnEmpty' => false],
            ['bars_flag', 'boolean'],
            [['title_over', 'admin_message'], 'string'],
            ['period_over', 'integer'],
            [['period_over_flag', 'admin_flag'], 'boolean'],
            ['executor_over_id', 'safe'],
            [['period_over', 'title_over', 'executor_over_id'], 'required', 'when' => function ($model) {
                return $model->period_over_flag;
            }, 'enableClientValidation' => false],
            [['admin_message'], 'required', 'when' => function ($model) {
                return $model->admin_flag;
            }, 'enableClientValidation' => false],
//            [['places', 'auditory_id'], 'required', 'when' => function ($model) {
//                return empty($model->places) && empty($model->auditory_id);
//            }, 'enableClientValidation' => false],
            [['auditory_id'], 'required', 'when' => function ($model) {
                return $model->formPlaces == 1;
            }, 'enableClientValidation' => false],
            [['places'], 'required', 'when' => function ($model) {
                return $model->formPlaces == 2;
            }, 'enableClientValidation' => false],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['author_id' => 'id']],
            [['signer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['signer_id' => 'id']],
            [['formPlaces'], 'safe'],
            [['protocol_members_list','protocol_subject_list','protocol_class_list'], 'safe'],
            [['protocol_leader_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['protocol_leader_id' => 'id']],
            [['protocol_secretary_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['protocol_secretary_id' => 'id']],
            [['protocol_secretary_id','protocol_members_list','protocol_subject_list','protocol_class_list'], 'required', 'when' => function ($model) {
                return $model->category->commission_sell == 1;
            }, 'enableClientValidation' => false],

        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['formPlaces', 'title_over']);
    }

    public function compareTimestamp($attribute, $params, $validator)
    {
        $timestamp_in = Yii::$app->formatter->asTimestamp($this->datetime_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($this->datetime_out);

        if ($this->datetime_out && $timestamp_in >= $timestamp_out) {
            $message = 'Время окончания мероприятия не может быть меньше или равно времени начала.';
            $this->addError($attribute, $message);
        }
    }


    public function checkFormatDateTime($attribute, $params)

    {
        if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])(-|\.)(0[1-9]|1[0-2])(-|\.)[0-9]{4}(\s)([01]?[0-9]|2[0-3])(:|\.)[0-5][0-9]$/", $this->$attribute)) {
            $this->addError($attribute, 'Формат ввода даты и времени не верен.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор мероприятия',
            'signer_id' => 'Подписант',
            'title' => 'Название мероприятия',
            'datetime_in' => 'Дата и время начала',
            'datetime_out' => 'Дата и время окончания',
            'places' => 'Место проведения',
            'auditory_id' => 'Аудитория',
            'department_list' => 'Отделы',
            'executors_list' => 'Ответственные',
            'category_id' => 'Категория мероприятия',
            'activities_over_id' => 'ИД мероприятия вне плана (подготовка к мероприятию)',
            'form_partic' => 'Форма участия',
            'partic_price' => 'Стоимость участия',
            'visit_poss' => 'Возможность посещения',
            'visit_content' => 'Комментарий по посещению',
            'important_event' => 'Значимость мероприятия',
            'format_event' => 'Формат мероприятия',
            'region_partners' => 'Зарубежные и региональные партнеры',
            'site_url' => 'Ссылка на мероприятие (сайт/соцсети)',
            'site_media' => 'Ссылка на медиаресурс',
            'description' => 'Описание мероприятия',
            'rider' => 'Технические требования',
            'result' => 'Итоги мероприятия',
            'num_users' => 'Количество участников',
            'num_winners' => 'Количество победителей',
            'num_visitors' => 'Количество зрителей',
            'bars_flag' => 'Отправлено в БАРС',
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'doc_status' => 'Статус мероприятия',
            'title_over' => 'Комментарий',
            'period_over' => 'Время подготовки к мероприятию',
            'period_over_flag' => 'Добавить подготовку к мероприятию',
            'executor_over_id' => 'Ответственный за подготовку',
            'admin_message' => 'Сообщение админа',
            'formPlaces' => 'Вид мероприятия',
            'protocol_leader_id' => 'Реководитель комиссии',
            'protocol_secretary_id' => 'Секретарь комиссии',
            'protocol_members_list' => 'Члены комиссии',
            'protocol_subject_list' => 'Дисциплины',
            'protocol_class_list' => 'Классы',
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    public function getFormPlaces()
    {
        if ($this->auditory_id != null) {
            return 1;
        } elseif ($this->places != '') {
            return 2;
        }
    }

    public function getTitleOver()
    {
        return $this->title_over == null ? 'Подготовка к мероприятию' : $this->title_over;
    }

    /**
     * Gets query for [[Auditory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::class, ['id' => 'auditory_id']);
    }

    public function getAuditoryName()
    {
        return $this->auditory ? $this->auditory->name : null;
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(GuidePlanTree::class, ['id' => 'category_id']);
    }

    public function getCategoryName()
    {
        return $this->category->name;
    }

    public function getCategorySell()
    {
        return isset($this->category) ? $this->category->category_sell : null;
    }

    /**
     * Gets query for [[ActivitiesOver]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivitiesOver()
    {
        return $this->hasOne(ActivitiesOver::class, ['id' => 'activities_over_id'])->andWhere(['over_category' => 2]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersEfficiency()
    {
        return $this->hasMany(TeachersEfficiency::class, ['item_id' => 'id'])->andWhere(['class' => StringHelper::basename(get_class($this))]);
    }

    /**
     * Список ответственных
     * @return array
     */
    public function getExecutorsList()
    {
        $query = (new Query())->from('teachers_view')
            ->select('teachers_id as id , fullname as name')
            ->where(['teachers_id' => $this->executors_list])
            ->all();
        return \yii\helpers\ArrayHelper::map($query, 'id', 'name');
    }

    public static function getEfficiencyForExecutors($models)
    {
        $data = [];
        $ids = \yii\helpers\ArrayHelper::getColumn($models, 'id');
        $query = TeachersEfficiency::find()
            ->where(['item_id' => $ids])
            ->andWhere(['class' => StringHelper::basename(self::class)])
            ->asArray()
            ->all();
        $query = \yii\helpers\ArrayHelper::index($query, null, ['item_id', 'teachers_id']);
        foreach ($query as $item_id => $teachers) {
            foreach ($teachers as $teachers_id => $value) {
                $label = [];
                foreach ($value as $index => $val) {
                    $label[] = Html::a($val['bonus'] . ($val['bonus_vid_id'] == 1 ? '%' : '₽'),
                        ['/schoolplan/default/teachers-efficiency', 'id' => $val['item_id']],
                        [
                            'style' => 'color: #428bca;
                                            padding: 2px 1px;
                                            text-decoration: none;
                                            cursor: pointer;
                                            border-bottom: 1px dashed;',
                            'data-pjax' => '0',
                        ]);
                }
                $data[$item_id][$teachers_id] = implode(',', $label);
            }
        }
        return $data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(UserCommon::class, ['id' => 'author_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'signer_id']);
    }

    public function getSchoolplanPerform()
    {
        return $this->hasMany(SchoolplanPerform::class, ['schoolplan_id' => 'id']);
    }

    public function getSchoolplanProtocol()
    {
        return $this->hasMany(SchoolplanProtocol::class, ['schoolplan_id' => 'id']);
    }

    /**
     * getFormParticList
     * @return array
     */
    public static function getFormParticList()
    {
        return self::FORM_PARTIC;
    }

    /**
     * getFormPlacesList
     * @return array
     */
    public static function getFormPlacesList()
    {
        return self::FORM_PLACES;
    }

    /**
     * getFormParticValue
     * @param string $val
     * @return string
     */
    public static function getFormParticValue($val)
    {
        $ar = self::getFormParticList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * getVisitPossList
     * @return array
     */
    public static function getVisitPossList()
    {
        return self::VISIT_POSS;
    }

    /**
     * getVisitPossValue
     * @param string $val
     * @return string
     */
    public static function getVisitPossValue($val)
    {
        $ar = self::getVisitPossList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * getImportantList
     * @return array
     */
    public static function getImportantList()
    {
        return self::IMPORTANT;
    }

    /**
     * getImportantValue
     * @param string $val
     * @return string
     */
    public static function getImportantValue($val)
    {
        $ar = self::getImportantList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * getFormatList
     * @return array
     */
    public static function getFormatList()
    {
        return self::FORMAT;
    }

    /**
     * getFormatValue
     * @param string $val
     * @return string
     */
    public static function getFormatValue($val)
    {
        $ar = self::getFormatList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * getPeriodOverList
     * @return array
     */
    public static function getPeriodOverList()
    {
        return self::PERIOD_OVER;
    }

    /**
     * getPeriodOverValue
     * @param string $val
     * @return string
     */
    public static function getPeriodOverValue($val)
    {
        $ar = self::getPeriodOverList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return bool
     */
    public function initActivitiesOver()
    {
        if ($model = $this->activitiesOver) {
            $this->period_over_flag = true;
            $this->period_over = (Yii::$app->formatter->asTimestamp($model->datetime_out) - Yii::$app->formatter->asTimestamp($model->datetime_in)) / 60;
            $this->title_over = $model->title;
            $this->executor_over_id = $model->executors_list;
            return true;
        } else {
        }
        return false;
    }

    /**
     * Добавление Подготовки к мероприятию
     * common/models/activities/ActivitiesOver
     *
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function setActivitiesOver($id = null)
    {
        if ($this->period_over_flag) {
            $transaction = \Yii::$app->db->beginTransaction();
            $timestamp = Yii::$app->formatter->asTimestamp($this->datetime_in) - $this->period_over * 60;
            $model = $id ? ActivitiesOver::findOne($id) : new ActivitiesOver();
            $model->auditory_id = $this->auditory_id;
            $model->datetime_in = Yii::$app->formatter->asDatetime($timestamp);
            $model->datetime_out = $this->datetime_in;
            $model->title = $this->title_over;
            $model->over_category = 2;
            $model->department_list = $this->department_list;
            $model->executors_list = [$this->executor_over_id];
            if ($model->save(false)) {
                $this->activities_over_id = $model->id;
                if ($this->save(false)) {
                    $transaction->commit();
                    return true;
                }
            }
            $transaction->rollBack();
            return false;
        } else {
            $this->deleteActivitiesOver();
            return $this->save(false);
        }
    }

    /**
     * @return false|int
     * @throws \yii\db\StaleObjectException
     */
    protected function deleteActivitiesOver()
    {
        if ($this->activities_over_id) {
            $model = ActivitiesOver::findOne($this->activities_over_id);
            return $model->delete();
        }
    }


    /**
     * @return bool|false|int
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->deleteActivitiesOver();
            if ($this->teachersEfficiency) {
                foreach ($this->teachersEfficiency as $model) {
                    $model->delete();
                }
            }
            return true;
        } else {
            return false;
        }
    }


    public function modifMessage()
    {
        $userCommon = UserCommon::findOne($this->author_id);
        $receiverId = $userCommon->user ? $userCommon->user->id : null;
        Yii::$app->mailbox->send($receiverId, 'modif', $this, $this->admin_message);
    }

    public function approveMessage()
    {
        $userCommon = UserCommon::findOne($this->author_id);
        $receiverId = $userCommon->user ? $userCommon->user->id : null;
        Yii::$app->mailbox->send($receiverId, 'approve', $this, $this->admin_message);
    }

    public function sendApproveMessage()
    {
        $receiverId = $this->signer_id;
        Yii::$app->mailbox->send($receiverId, 'send_approve', $this, $this->admin_message);
    }

    /**
     * @return |null
     */
    public static function getAuthorId()
    {
        $id = \Yii::$app->user->id;
        $user = User::findOne($id);
        return $user->userCommon ? $user->userCommon->id : null;
    }

    /**
     * @return bool|string
     */
    public static function getAuthorEmail()
    {
        $id = \Yii::$app->user->id;
        $user = User::findOne($id);
        return $user->email ?? false;
    }

    /**
     * @return bool
     */
    public function isAuthor()
    {
        return $this->author_id == self::getAuthorId();
    }

    public static function getSignerId()
    {
        $id = \Yii::$app->user->id;
        $user = User::findOne($id);
        return $user ? $user->id : null;
    }

    public function isSigner()
    {
        return $this->signer_id == self::getSignerId();
    }

    /**
     * @return |null
     */
    public static function getOwnerId()
    {
        return Yii::$app->user->identity->getId();
    }
    /**
     * Залогинился секретарь или руководитель
     * @return bool
     */
    public function isProtocolSigner()
    {
        return in_array(self::getOwnerId(), [$this->protocol_secretary_id, $this->protocol_leader_id]);
    }

    /**
     * Залогинился ответственный за мероприятие или член комиссии
     * @return bool
     */
    public function isExecutors()
    {

        $ids = (new Query())->from('teachers_view')
            ->select('user_id')
            ->where(['teachers_id' => $this->executors_list])
            ->andWhere(['=', 'status', UserCommon::STATUS_ACTIVE])
            ->column();
        return in_array(self::getOwnerId(), $ids);
    }

    public function isProtocolMembers()
    {
        return in_array(self::getOwnerId(), $this->protocol_members_list);
    }


    public function setSchuulplanProtocols()
    {
        $timestamp = Yii::$app->formatter->asTimestamp($this->datetime_in);
        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $timestamp);
        $data = TeachersLoadStudyplanView::find()
            ->select('studyplan_subject_id, studyplan_id,  teachers_id')
            ->distinct('studyplan_subject_id, studyplan_id,  teachers_id')
            ->where(['teachers_id' => $this->executors_list])
            ->andWhere(['subject_id' => $this->protocol_subject_list])
            ->andWhere(['course' => $this->protocol_class_list])
            ->andWhere(['=', 'plan_year', $plan_year])
            ->orderBy('teachers_id')
            ->asArray()
            ->all();
        foreach ($data as $item => $value) {
            $exists = SchoolplanProtocol::find()
                ->where(['schoolplan_id' => $this->id])
                ->andWhere(['studyplan_id' => $value['studyplan_id']])
                ->andWhere(['studyplan_subject_id' => $value['studyplan_subject_id']])
                ->andWhere(['teachers_id' => $value['teachers_id']])
                ->exists();
            if ($exists) continue;

            $model = new SchoolplanProtocol();
            $model->schoolplan_id = $this->id;
            $model->studyplan_id = $value['studyplan_id'];
            $model->studyplan_subject_id = $value['studyplan_subject_id'];
            $model->teachers_id = $value['teachers_id'];
            $model->save(false);
        }
//        echo '<pre>' . print_r($data, true) . '</pre>';
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->form_partic == 1) {
            $this->partic_price = null;
        }
        if ($this->getCategorySell() == 1 || $this->formPlaces == 1) {
            $this->places = null;
        } elseif ($this->getCategorySell() == 2 || $this->formPlaces == 2) {
            $this->auditory_id = null;
        }
        if($this->category->commission_sell == 1) {
            $this->setSchuulplanProtocols();
        }
        return parent::beforeSave($insert);
    }
}
