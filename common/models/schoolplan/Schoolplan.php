<?php

namespace common\models\schoolplan;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\fileinput\behaviors\FileManagerBehavior;
use common\models\auditory\Auditory;
use common\models\guidesys\GuidePlanTree;
use phpDocumentor\Reflection\Types\Self_;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "schoolplan".
 *
 * @property int $id
 * @property string|null $name Название мероприятия
 * @property int $datetime_in Дата и время начала
 * @property int $datetime_out Дата и время окончания
 * @property string|null $places Место проведения
 * @property int|null $auditory_id Аудитория
 * @property string|null $department_list Отделы
 * @property string|null $executors_list Ответственные
 * @property int $category_id Категория мероприятия
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
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 * @property int $bars_flag
 *
 * @property Auditory $auditory
 * @property GuidePlanTree $category
 */
class Schoolplan extends \artsoft\db\ActiveRecord
{
    const FORM_PARTIC = [
        1 => 'Беcплатное',
        2 => 'Платное',
    ];

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
            [['name', 'datetime_in', 'datetime_out', 'category_id'], 'required'],
            [['department_list', 'executors_list', 'category_id'], 'required'],
            [['partic_price'], 'required', 'when' => function($model) {
                return $model->form_partic == '2'; }, 'enableClientValidation' => false],
             [['places'], 'required', 'when' => function($model) {
                return $model->getPlanCategorySell() == 2; }, 'enableClientValidation' => false],
            [['auditory_id'], 'required', 'when' => function($model) {
                return $model->getPlanCategorySell() == 1; }, 'enableClientValidation' => false],
            [['department_list', 'executors_list', 'datetime_in', 'datetime_out'], 'safe'],
            [['auditory_id', 'category_id', 'form_partic', 'visit_poss', 'important_event', 'format_event', 'num_users', 'num_winners', 'num_visitors'], 'integer'],
            [['visit_content', 'region_partners', 'rider', 'result'], 'string'],
            [['site_url', 'site_media'], 'url', 'defaultScheme' => 'http'],
            [['name'], 'string', 'max' => 100],
            [['places'], 'string', 'max' => 512],
            [['partic_price', 'site_url', 'site_media'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 4000, 'min' => 1000],
            [['auditory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditory::class, 'targetAttribute' => ['auditory_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuidePlanTree::class, 'targetAttribute' => ['category_id' => 'id']],
            [['datetime_out'], 'compareTimestamp', 'skipOnEmpty' => false],
            ['bars_flag', 'boolean']
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
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название мероприятия',
            'datetime_in' => 'Дата и время начала',
            'datetime_out' => 'Дата и время окончания',
            'places' => 'Место проведения',
            'auditory_id' => 'Аудитория',
            'department_list' => 'Отделы',
            'executors_list' => 'Ответственные',
            'category_id' => 'Категория мероприятия',
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

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(GuidePlanTree::class, ['id' => 'category_id']);
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

    public function getPlanCategory()
    {
        return $this->hasOne(GuidePlanTree::class, ['id' => 'category_id']);
    }

    public function getPlanCategoryName()
    {
        return $this->planCategory->name;
    }

    public function getPlanCategorySell()
    {
        return $this->planCategory->category_sell;
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
        if ($this->getPlanCategorySell() == 1) {
            $this->places = null;
        } else {
            $this->auditory_id = null;
        }
        return parent::beforeSave($insert);
    }
}
