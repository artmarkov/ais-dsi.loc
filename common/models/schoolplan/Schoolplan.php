<?php

namespace common\models\schoolplan;

use artsoft\Art;
use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\Notice;
use common\models\activities\ActivitiesOver;
use common\models\auditory\Auditory;
use common\models\education\LessonMark;
use common\models\efficiency\TeachersEfficiency;
use common\models\guidesys\GuidePlanTree;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanThematicItems;
use common\models\teachers\TeachersLoadStudyplanView;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

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
 * @property int $protocol_leader_id Председатель комиссии user_id
 * @property strung $protocol_leader_name Председатель комиссии (введено вручную)
 * @property int $protocol_soleader_id Заместитель председателя комиссии user_id
 * @property int $protocol_secretary_id Секретарь комиссии user_id
 * @property string $protocol_members_list Члены комиссии user_id
 * @property string $protocol_class_list Классы
 * @property int $protocol_subject_cat_id Категория дисциплины
 * @property int $protocol_subject_id Дисциплина
 * @property int $protocol_subject_vid_id Вид дисциплины(групповое, инд)
 *
 * @property Auditory $auditory
 * @property Author $author
 * @property User $user
 * @property GuidePlanTree $category
 * @property ActivitiesOver $activitiesOver
 * @property TeachersEfficiency $teachersEfficiency
 * @property SchoolplanPerform $schoolplanPerform
 * @property SchoolplanProtocol $schoolplanProtocol
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
    public $protocolFlag;
    public $protocolLeaderFlag;
    public $date_in;
    public $time_in;
    public $date_out;
    public $time_out;

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
                'attributes' => ['department_list', 'executors_list', 'protocol_members_list', 'protocol_class_list', 'protocol_subject_id'],
            ],
            'fileManager' => [
                'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
                'form_name' => 'Schoolplan',
            ],
        ];
    }

    public function beforeValidate()
    {
        $this->datetime_in = $this->date_in . ' ' . $this->time_in;
        $this->datetime_out = $this->date_out . ' ' . $this->time_out;
        return parent::beforeValidate();
    }

    public function afterFind()
    {
        $this->date_in = Yii::$app->formatter->asDate($this->datetime_in);
        $this->time_in = Yii::$app->formatter->asDatetime($this->datetime_in, 'php:H:i');
        $this->date_out = Yii::$app->formatter->asDate($this->datetime_out);
        $this->time_out = Yii::$app->formatter->asDatetime($this->datetime_out, 'php:H:i');
        parent::afterFind();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', /*'datetime_in', 'datetime_out',*/ 'category_id', 'author_id', 'signer_id'], 'required'],
            [['date_in', 'time_in', 'date_out', 'time_out'], 'required'],
            [['department_list', 'executors_list'], 'required'],
            [['partic_price'], 'required', 'when' => function ($model) {
                return $model->form_partic == '2';
            }, 'enableClientValidation' => false],
            [['department_list', 'executors_list', 'datetime_in', 'datetime_out'], 'safe'],
            [['auditory_id', 'category_id', 'activities_over_id', 'form_partic', 'visit_poss', 'important_event', 'format_event', 'num_users', 'num_winners', 'num_visitors', 'author_id', 'signer_id'], 'integer'],
            [['protocol_leader_id', 'protocol_soleader_id', 'protocol_secretary_id'], 'integer'],
            [['protocol_subject_cat_id', 'protocol_subject_vid_id'], 'integer'],
            [['visit_content', 'region_partners', 'rider', 'result'], 'string'],
            [['site_url', 'site_media'], 'url', 'defaultScheme' => 'http'],
            [['title'], 'string', 'max' => 512],
            [['places'], 'string', 'max' => 512],
            [['protocol_leader_name'], 'string', 'max' => 127],
            [['description'], 'default', 'value' => null],
            [['doc_status'], 'default', 'value' => 0],
            [['category_id'], 'default', 'value' => 17],
            [['partic_price', 'site_url', 'site_media'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 4000, 'when' => function ($model) {
                return isset($model->category) && $model->category->description_flag && !$model->isNewRecord;
            }, 'enableClientValidation' => false, 'skipOnEmpty' => false, 'message' => 'Введите описание мероприятия минимум 1000 знаков, включая пробелы.'],
            ['description', 'string', 'max' => 4000, 'when' => function ($model) {
                return isset($model->category) && !$model->category->description_flag && !$model->isNewRecord;
            }, 'enableClientValidation' => false, 'message' => 'Введите описание мероприятия минимум 500 знаков, включая пробелы.'],
            [['auditory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditory::class, 'targetAttribute' => ['auditory_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuidePlanTree::class, 'targetAttribute' => ['category_id' => 'id']],
            [['activities_over_id'], 'exist', 'skipOnError' => true, 'targetClass' => ActivitiesOver::class, 'targetAttribute' => ['activities_over_id' => 'id']],
            [['datetime_in', 'datetime_out'], 'checkFormatDateTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['date_out'], 'compareTimestamp', 'skipOnEmpty' => false],
            [['date_in'], 'addNew', 'skipOnEmpty' => false, 'when' => function ($model) {
                return Art::isFrontend() && $model->isNewRecord;
            }, 'enableClientValidation' => false],
            [['date_in', 'time_in', 'date_out', 'time_out'], 'safe'],
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
            }, 'whenClient' => "function (attribute, value) {
                                return document.querySelector('input[type=\"radio\"][name=\"Schoolplan[formPlaces]\"]:checked').value == 1;
                            }"],
            [['places'], 'required', 'when' => function ($model) {
                return $model->formPlaces == 2;
            }, 'whenClient' => "function (attribute, value) {
                                return document.querySelector('input[type=\"radio\"][name=\"Schoolplan[formPlaces]\"]:checked').value == 2;
                            }"],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['author_id' => 'id']],
            [['signer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['signer_id' => 'id']],
            [['formPlaces'], 'safe'],
            [['protocolLeaderFlag', 'protocolFlag'], 'boolean'],
            [['protocol_members_list', 'protocol_class_list', 'protocol_subject_id'], 'safe'],
//            [['protocol_leader_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['protocol_leader_id' => 'id']],
//            [['protocol_soleader_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['protocol_leader_id' => 'id']],
            [['protocol_secretary_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['protocol_secretary_id' => 'id']],
            [['protocol_members_list', 'protocol_class_list', 'protocol_subject_cat_id', 'protocol_subject_id', 'protocol_subject_vid_id'], 'required', 'when' => function ($model) {
                return $model->protocolFlag == true;
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"schoolplan-protocolflag\"]').prop('checked') === true;
                            }"],
//            [['protocol_leader_name'], 'required', 'when' => function ($model) {
//                return $model->protocolFlag == true && $model->protocolLeaderFlag == true && $model->category->commission_sell == 1 && !$model->isNewRecord;
//            },
//                'whenClient' => "function (attribute, value) {
//                                return $('input[id=\"schoolplan-protocolleaderflag\"]').prop('checked') === true && $('input[id=\"schoolplan-protocolflag\"]').prop('checked') === true;
//                            }"],
//            [['protocol_leader_id'], 'required', 'when' => function ($model) {
//                return $model->protocolFlag == true && $model->protocolLeaderFlag == false && $model->category->commission_sell == 1 && !$model->isNewRecord;
//            },
//                'whenClient' => "function (attribute, value) {
//                                return $('input[id=\"schoolplan-protocolleaderflag\"]').prop('checked') === false && $('input[id=\"schoolplan-protocolflag\"]').prop('checked') === true;
//                            }"],
            [['date_in'], 'validateOwnSchoolplan', 'skipOnEmpty' => false, 'skipOnError' => false],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['formPlaces', 'title_over', 'protocolLeaderFlag']);
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

    public function addNew($attribute, $params, $validator)
    {
        $timestamp_in = Yii::$app->formatter->asTimestamp($this->datetime_in);

        if ($this->datetime_in && $timestamp_in < time()) {
            $message = 'Нельзя веести мероприятие задним числом.';
            $this->addError($attribute, $message);
        }
    }

    public function validateOwnSchoolplan($attribute, $params, $validator)
    {
        $message = '';
        if($this->auditory_id) {
            if ($this->getOwnSchoolplanOverLapping()->exists() === true) {
                $info = [];
                foreach ($this->getOwnSchoolplanOverLapping()->all() as $itemModel) {
                    $info[] = ' ' . $itemModel->datetime_in . ' - ' . $itemModel->datetime_out . ' (' . $itemModel->title . ')';
                }
                $message = 'Накладка по времени - По плану работы: ' . implode('; ', $info);
                Notice::registerDanger($message);
            }
            if ($this->getActivitiesOverOverLapping()->exists() === true) {
                $info = [];
                foreach ($this->getActivitiesOverOverLapping()->all() as $itemModel) {
                    $info[] = ' ' . $itemModel->datetime_in . ' - ' . $itemModel->datetime_out . ' (' . $itemModel->title . ')';
                }
                $message = 'Накладка по времени - Вне плана: ' . implode('; ', $info);
                Notice::registerDanger($message);
            }
            if (!empty($info)) {
                $this->addError($attribute, 'В одной аудитории накладка по времени!' . ' ' . $message);
            }
        }
    }

    public function getOwnSchoolplanOverLapping()
    {
        $thereIsAnOverlapping = self::find()->where(
            ['AND',
                ['!=', 'id', $this->id],
                ['auditory_id' => $this->auditory_id],
                ['OR',
                    ['AND',
                        ['<', 'datetime_in', Yii::$app->formatter->asTimestamp($this->datetime_out)],
                        ['>=', 'datetime_in', Yii::$app->formatter->asTimestamp($this->datetime_in)],
                    ],
                    ['AND',
                        ['<=', 'datetime_out', Yii::$app->formatter->asTimestamp($this->datetime_out)],
                        ['>', 'datetime_out', Yii::$app->formatter->asTimestamp($this->datetime_in)],
                    ],
                ],
            ]);

        return $thereIsAnOverlapping;
    }

    public function getActivitiesOverOverLapping()
    {
        $thereIsAnOverlapping = ActivitiesOver::find()->where(
            ['AND',
                ['!=', 'id', $this->activities_over_id],
                ['auditory_id' => $this->auditory_id],
                ['OR',
                    ['AND',
                        ['<', 'datetime_in', Yii::$app->formatter->asTimestamp($this->datetime_out)],
                        ['>=', 'datetime_in', Yii::$app->formatter->asTimestamp($this->datetime_in)],
                    ],
                    ['AND',
                        ['<=', 'datetime_out', Yii::$app->formatter->asTimestamp($this->datetime_out)],
                        ['>', 'datetime_out', Yii::$app->formatter->asTimestamp($this->datetime_in)],
                    ],
                ],
            ]);

        return $thereIsAnOverlapping;
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
            'protocol_leader_id' => 'Председатель комиссии',
            'protocol_leader_name' => 'Председатель комиссии',
            'protocol_soleader_id' => 'Заместитель председателя комиссии',
            'protocol_secretary_id' => 'Секретарь комиссии',
            'protocol_members_list' => 'Члены комиссии',
            'protocol_class_list' => 'Классы',
            'protocol_subject_cat_id' => 'Категория дисциплины',
            'protocol_subject_id' => 'Дисциплина',
            'protocol_subject_vid_id' => 'Вид дисциплины',
            'date_in' => 'Дата начала',
            'time_in' => 'Время начала',
            'date_out' => 'Дата окончания',
            'time_out' => 'Время окончания',
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    public function getFormPlaces()
    {
        if ($this->auditory_id) {
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
       // print_r($this->executor_over_id);die();
        if ($this->period_over_flag) {
            if(is_array($this->executor_over_id)) {
                $this->executor_over_id = $this->executor_over_id[0];
            }
            $transaction = \Yii::$app->db->beginTransaction();
            $timestamp = Yii::$app->formatter->asTimestamp($this->datetime_in) - $this->period_over * 60;
            $model = $id ? ActivitiesOver::findOne($id) : new ActivitiesOver();
            $model->auditory_id = $this->auditory_id;
            $model->datetime_in = Yii::$app->formatter->asDatetime($timestamp);
            $model->datetime_out = $this->datetime_in;
            $model->title = $this->title_over;
            $model->over_category = 2;
            $model->department_list = $this->department_list;
            $model->executors_list = [$this->executor_over_id] /*Art::isBackend() ? [$this->executor_over_id] : [$this->executor_over_id[0]]*/; // TODO не пойму в чем проблема костыль!
            if ($model->save(false)) {
                $this->activities_over_id = $model->id;
                $transaction->commit();
                return true;
            }
            $transaction->rollBack();
            return false;
        } else {
            return $this->deleteActivitiesOver();
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
        return true;
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
     * Залогинился секретарь, председатель или зам.председателя
     * @return bool
     */
    public function isProtocolSigner()
    {
        return in_array(self::getOwnerId(), [$this->protocol_secretary_id, $this->protocol_leader_id, $this->protocol_soleader_id]);
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


    public function setSchoolplanProtocols()
    {
        $timestamp = Yii::$app->formatter->asTimestamp($this->datetime_in);
        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $timestamp);
        $data = TeachersLoadStudyplanView::find()
            ->select('studyplan_subject_id, teachers_id')
            ->distinct('studyplan_subject_id, teachers_id')
            ->where(['direction_id' => 1000])
            ->andWhere(['=', 'plan_year', $plan_year])
            ->andWhere(['subject_cat_id' => $this->protocol_subject_cat_id])
            ->andWhere(['subject_id' => $this->protocol_subject_id])
            ->andWhere(['subject_vid_id' => $this->protocol_subject_vid_id])
            ->andWhere(['course' => $this->protocol_class_list])
            ->andWhere(['fin_cert' => true])
            ->orderBy('teachers_id')
            ->asArray()
            ->all();
        foreach ($data as $item => $value) {
            $exists = SchoolplanProtocol::find()
                ->where(['schoolplan_id' => $this->id])
                ->andWhere(['studyplan_subject_id' => $value['studyplan_subject_id']])
                ->andWhere(['teachers_id' => $value['teachers_id']])
                ->exists();
            if ($exists) continue;

            $model = new SchoolplanProtocol();
            $model->schoolplan_id = $this->id;
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
        if ($this->protocolLeaderFlag) {
            $this->protocol_leader_id = null;
        } else {
            $this->protocol_leader_name = null;
        }
        if ($this->category->protocol_flag && $this->protocol_subject_vid_id == 1000) { // добавляем только для индивидуальных занятий, где требуется вводить программу для каждого ученика
            $this->setSchoolplanProtocols();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Нахождение всех элементов репертуарного плана для $studyplan_subject_id
     * @param $studyplan_subject_id
     * @param $teachers_id
     * @return array
     * @throws \yii\db\Exception
     */
    public function getStudyplanThematicItemsById($studyplan_subject_id, $teachers_id)
    {
        $studyplan_subject_id = is_array($studyplan_subject_id) ? implode(',', $studyplan_subject_id) : $studyplan_subject_id;

        return Yii::$app->db->createCommand(' SELECT DISTINCT studyplan_thematic_items.id as id,
		                  studyplan_thematic_items.topic AS name
                    FROM studyplan_thematic_view 
                    INNER JOIN studyplan_thematic_items ON studyplan_thematic_view.studyplan_thematic_id = studyplan_thematic_items.studyplan_thematic_id 
                    where  studyplan_subject_id = ANY (string_to_array(:studyplan_subject_id, \',\')::int[]) 
                    AND studyplan_thematic_items.topic != \'\'
                    AND studyplan_thematic_view.teachers_id = :teachers_id',
            [
                'studyplan_subject_id' => $studyplan_subject_id,
                'teachers_id' => $teachers_id
            ])->queryAll();
    }


    public function getThematicItemsByStudyplanSubject($studyplan_subject_id, $teachers_id)
    {
        return ArrayHelper::map($this->getStudyplanThematicItemsById($studyplan_subject_id, $teachers_id), 'id', 'name');
    }

    /**
     * Выборка ученик-предмет для проподакателя в рамках протокола
     * @param $teachers_id
     * @return array|TeachersLoadStudyplanView[]|\yii\db\ActiveRecord[]
     */
    public function getStudyplanSubjectListById($teachers_id)
    {
        $timestamp = Yii::$app->formatter->asTimestamp($this->datetime_in);
        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $timestamp);

        return TeachersLoadStudyplanView::find()
            ->select('studyplan_subject_id as id,  student_fio as name')
            ->distinct('studyplan_subject_id, student_fio')
            ->where(['=', 'teachers_id', $teachers_id])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['=', 'plan_year', $plan_year])
            ->andWhere(['subject_cat_id' => $this->protocol_subject_cat_id])
            ->andWhere(['subject_id' => $this->protocol_subject_id])
//            ->andWhere(['subject_vid_id' => $this->protocol_subject_vid_id])
//            ->andWhere(['course' => $this->protocol_class_list])
            ->asArray()
            ->all();
    }

    public function getStudyplanSubjectListByTeachers($teachers_id)
    {
        return \yii\helpers\ArrayHelper::map(self::getStudyplanSubjectListById($teachers_id), 'id', 'name');
    }

    /**
     * Возможные преподавателей для протокола
     * @return array
     */
    public function getTeachersListForProtocol()
    {
        $timestamp = Yii::$app->formatter->asTimestamp($this->datetime_in);
        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $timestamp);

        $teachersIds = TeachersLoadStudyplanView::find()
            ->select('teachers_id')
            ->distinct('teachers_id')
            ->where(['=', 'plan_year', $plan_year])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['subject_cat_id' => $this->protocol_subject_cat_id])
            ->andWhere(['subject_id' => $this->protocol_subject_id])
//            ->andWhere(['subject_vid_id' => $this->protocol_subject_vid_id])
            ->column();
        $modelsTeachers = (new Query())->from('teachers_view')->where(['teachers_id' => $teachersIds])->all();
        return \yii\helpers\ArrayHelper::map($modelsTeachers, 'teachers_id', 'fio');
    }

    /**
     * Печать протокола
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function makeProtocolDocx()
    {
        $model = $this;
        $template = 'document/schoolplan_protocol.docx';
        $timestamp = Yii::$app->formatter->asTimestamp($this->datetime_in);
        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $timestamp);

        if (!isset($model->schoolplanProtocol)) {
            throw new NotFoundHttpException("The SchoolplanProtocol was not found.");
        }
        $modelsProtocol = $model->schoolplanProtocol;

        $studyplanSubjectIds = ArrayHelper::getColumn($modelsProtocol, 'studyplan_subject_id');
        $modelsStudent = (new \yii\db\Query())->from('studyplan_subject_view')->where(['studyplan_subject_id' => $studyplanSubjectIds])->all();
        $modelsStudent = \yii\helpers\ArrayHelper::map($modelsStudent, 'studyplan_subject_id', 'memo_5');

        $teachersIds = ArrayHelper::getColumn($modelsProtocol, 'teachers_id');
        $modelsTeachers = (new Query())->from('teachers_view')->where(['teachers_id' => $teachersIds])->all();
        $modelsTeachers = \yii\helpers\ArrayHelper::map($modelsTeachers, 'teachers_id', 'fio');
        $modelsMark = LessonMark::find()->asArray()->all();
        $markLabelList = \yii\helpers\ArrayHelper::map($modelsMark, 'id', 'mark_label');
        $markHintsList = \yii\helpers\ArrayHelper::map($modelsMark, 'id', 'mark_hint');
//        echo '<pre>' . print_r($modelsMark, true) . '</pre>';
//        die();
        $sign = $subjects = [];
        foreach ($model->protocol_members_list as $id) {
            $user = UserCommon::findOne(['user_id' => $id]);
            $sign[] = [
                'rank' => 's',
                'protocol_member_fullname' => $user ? $user->getFullName() : '',
                'protocol_member_fio' => $user ? $user->getLastFM() : '',
            ];
        }

        $protocol_leader = UserCommon::findOne(['user_id' => $model->protocol_leader_id]);
        $protocol_soleader = UserCommon::findOne(['user_id' => $model->protocol_soleader_id]);
        $protocol_secretary = UserCommon::findOne(['user_id' => $model->protocol_secretary_id]);

        foreach ($this->protocol_subject_id as $subject) {
            $subjects[] = RefBook::find('subject_name')->getValue($subject);
        }

        $data[] = [
            'rank' => 'doc',
            'date' => Yii::$app->formatter->asDate($timestamp),
            'plan_year' => ArtHelper::getStudyYearsValue($plan_year),
            'subject_name' => RefBook::find('subject_category_name')->getValue($this->protocol_subject_cat_id) . ': ' . implode(', ', $subjects),
            'protocol_leader_fullname' => $model->protocol_leader_name != null ? $model->protocol_leader_name : ($protocol_leader ? $protocol_leader->getFullName() : ''),
            'protocol_soleader_fullname' => $protocol_soleader ? $protocol_soleader->getFullName() : '',
            'protocol_secretary_fullname' => $protocol_secretary ? $protocol_secretary->getFullName() : '',
            'protocol_members_fullname' => implode(', ', ArrayHelper::getColumn($sign, 'protocol_member_fullname')),
            'protocol_leader_fio' => $model->protocol_leader_name != null ? \artsoft\helpers\StringHelper::fullname2fio($model->protocol_leader_name) : ($protocol_leader ? $protocol_leader->getLastFM() : ''),
            'protocol_soleader_fio' => $protocol_soleader ? $protocol_soleader->getLastFM() : '',
            'protocol_secretary_fio' => $protocol_secretary ? $protocol_secretary->getLastFM() : '',

        ];
        $items = [];
        foreach ($modelsProtocol as $item => $modelProtocol) {
            if ($modelProtocol->thematic_items_list[0] != null) {
                $thematic_items_list = StudyplanThematicItems::find()->select('topic')->where(['id' => $modelProtocol->thematic_items_list])->column();
                $student_programm = implode(', ', $thematic_items_list);
            } else {
                $student_programm = $modelProtocol->task_ticket;
            }
            $items[] = [
                'rank' => 'a',
                'item' => $item + 1 . '.',
                'student_info' => $modelsStudent[$modelProtocol->studyplan_subject_id] . ', ' . $modelsTeachers[$modelProtocol->teachers_id],
                'student_programm' => $student_programm,
                'student_unswer' => isset($markLabelList[$modelProtocol->lesson_mark_id]) ? ($markLabelList[$modelProtocol->lesson_mark_id] . ' (' . $markHintsList[$modelProtocol->lesson_mark_id] . ')') : '',
                'student_resume' => $modelProtocol->resume,
            ];
        }

        $output_file_name = str_replace('.', '_' . $timestamp . '.', basename($template));

        $tbs = DocTemplate::get($template)->setHandler(function ($tbs) use ($data, $items, $sign) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('a', $items);
            $tbs->MergeBlock('s', $sign);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

    public static function getSchoolplanListForStudyplan($studyplan_id)
    {
        $model = Studyplan::findOne(['id' => $studyplan_id]);
        $timestamp = \artsoft\helpers\ArtHelper::getStudyYearParams($model->plan_year);
        $departmentList = TeachersLoadStudyplanView::find()
            ->select('department_list')
            ->where(['=', 'studyplan_id', $studyplan_id])
            ->andWhere(['IS NOT', 'teachers_id', NULL])
            ->column();
        $departmentList = implode(',', array_unique(explode(',', implode(',', $departmentList))));
        $query = self::find()->joinWith('category')
            ->select(new \yii\db\Expression('schoolplan.id as id, concat(TO_CHAR(to_timestamp(datetime_in) :: DATE, \'dd.mm.yyyy\'), \' - \', title) as name'))
            ->where(['between', 'datetime_in', $timestamp['timestamp_in'], $timestamp['timestamp_out']])
            ->andWhere(new Expression("string_to_array(department_list, ','::text)::text[] && string_to_array('{$departmentList}', ','::text)::text[]")) // сравнение массивов
            ->andWhere(['guide_plan_tree.perform_flag' => true])
            ->orderBy('datetime_in')
            ->asArray()
            ->all();
//        echo '<pre>' . print_r(ArrayHelper::map($query, 'id', 'name'), true) . '</pre>';
        return ArrayHelper::map($query, 'id', 'name');
    }

    public static function getExecutorsListById($studyplan_id)
    {
        if (!$studyplan_id ) return [];

        $teachersList = TeachersLoadStudyplanView::find()
            ->select('teachers_id')
            ->where(['=', 'studyplan_id', $studyplan_id])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['IS NOT', 'teachers_id', NULL])
            ->column();
        if (Art::isFrontend()) {
            $userId = Yii::$app->user->identity->getId();
            $teachers_id = RefBook::find('users_teachers')->getValue($userId) ?? null;
            if (in_array($teachers_id, $teachersList)) {
                $teachersList = $teachers_id;
            }
        }

        $query = (new Query())->from('teachers_view')
            ->select('teachers_id as id , fullname as name')
            ->where(['teachers_id' => $teachersList])
            ->all();
        return $query;
    }

    public static function getExecutorsListByName($studyplan_id)
    {
        if (!$studyplan_id) return [];
        return \yii\helpers\ArrayHelper::map(self::getExecutorsListById($studyplan_id), 'id', 'name');
    }
}
