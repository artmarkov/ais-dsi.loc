<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use common\models\activities\ActivitiesOver;
use common\models\auditory\Auditory;
use common\models\efficiency\TeachersEfficiency;
use common\models\guidesys\GuidePlanTree;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "schoolplan".
 *
 * @property int $id
 * @property int $author_id Автор записи
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
 * @property Auditory $auditory
 * @property Author $author
 * @property GuidePlanTree $category
 * @property ActivitiesOver $activitiesOver
 * @property TeachersEfficiency $teachersEfficiency
 */
class Schoolplan extends \artsoft\db\ActiveRecord
{
    public $period_over;
    public $period_over_flag;
    public $executor_over_id;
    public $title_over;

    public $admin_message;
    public $admin_flag;

    const FORM_PARTIC = [
        1 => 'Беcплатное',
        2 => 'Платное',
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
                'attributes' => ['department_list', 'executors_list'],
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
            [['title', 'datetime_in', 'datetime_out', 'category_id', 'author_id', 'doc_status'], 'required'],
            [['department_list', 'executors_list'], 'required'],
            [['partic_price'], 'required', 'when' => function ($model) {
                return $model->form_partic == '2';
            }, 'enableClientValidation' => false],
            [['department_list', 'executors_list', 'datetime_in', 'datetime_out'], 'safe'],
            [['auditory_id', 'category_id', 'activities_over_id', 'form_partic', 'visit_poss', 'important_event', 'format_event', 'num_users', 'num_winners', 'num_visitors', 'author_id'], 'integer'],
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
            ['description', 'string', 'max' => 4000, 'min' => 500, 'when' => function ($model) {
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
            [['places', 'auditory_id'], 'required', 'when' => function ($model) {
                return empty($model->places) && empty($model->auditory_id);
            }, 'enableClientValidation' => false],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['author_id' => 'id']],

        ];
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
        ];
    }

    public function optimisticLock()
    {
        return 'version';
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
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(UserCommon::class, ['id' => 'author_id']);
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

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->form_partic == 1) {
            $this->partic_price = null;
        }
        if ($this->getCategorySell() == 1) {
            $this->places = null;
        } else {
            $this->auditory_id = null;
        }
        return parent::beforeSave($insert);
    }

    public function sendAdminMessage($post)
    {
        if ($post) {
            $textBody = 'Сообщение модуля "План работы" ' . PHP_EOL;
            $htmlBody = '<p><b>Сообщение модуля "План работы"</b></p>';

            $textBody .= 'Прошу Вас внести уточнения в мероприятие: ' . strip_tags($post['title']) . ' от ' . strip_tags($post['datetime_in']) . PHP_EOL;
            $htmlBody .= '<p>Прошу Вас внести уточнения в мероприятие:' . strip_tags($post['title']) . ' от ' . strip_tags($post['datetime_in']) . '</p>';

            $textBody .= '--------------------------' . PHP_EOL;
            $textBody .= 'Сообщение создано автоматически. Отвечать на него не нужно.';
            $htmlBody .= '<hr>';
            $htmlBody .= '<p>Сообщение создано автоматически. Отвечать на него не нужно.</p>';

            return Yii::$app->mailqueue->compose()
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject('Сообщение с сайта ' . Yii::$app->name)
                ->setTextBody($textBody)
                ->setHtmlBody($htmlBody)
                ->queue();
        }
    }

}
