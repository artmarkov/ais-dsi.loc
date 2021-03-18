<?php

namespace common\models\teachers;

use common\models\guidejob\BonusItem;
use common\models\guidejob\Level;
use common\models\guidejob\Position;
use common\models\own\Department;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\user\User;

/**
 * This is the model class for table "{{%teachers}}".
 *
 * @property int $id
 * @property int $position_id
 * @property int $level_id
 * @property string $tab_num
 * @property int $timestamp_serv
 * @property int $timestamp_serv_spec
 * @property int $status
 *
 * @property TeachersLevel $level
 * @property TeachersPosition $position
 */
class Teachers extends \yii\db\ActiveRecord
{
    public $time_serv_init;
    public $time_serv_spec_init;
    public $year_serv;
    public $year_serv_spec;

    public $direction_id_main;
    public $stake_id_main;
    public $direction_id_optional;
    public $stake_id_optional;

    public $gridDepartmentSearch;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Кол-во секунд в году
     */
    const YEAR_SEC = 31536000; 
    /**
     * Дата учета рабочего времени (1 сентября текущего года)
     */
    const SERV_MON = 9;
    const SERV_DAY = 1;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teachers}}';
    }
    /**
     * Реализация поведения многое ко многим
     * @return  mixed
     */
    public function behaviors()
    {
        return [
            [
                'class' => \artsoft\behaviors\ManyHasManyBehavior::className(),
                'relations' => [
                    'bonusItem' => 'bonus_list',
                    'departmentItem' => 'department_list',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position_id', 'level_id', 'timestamp_serv', 'timestamp_serv_spec', 'status'], 'integer'],
            [['tab_num'], 'string', 'max' => 16],
            [['level_id'], 'exist', 'skipOnError' => true, 'targetClass' => Level::className(), 'targetAttribute' => ['level_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['bonus_list', 'department_list'], 'safe'],
            [['year_serv', 'year_serv_spec', 'time_serv_init', 'time_serv_spec_init'], 'safe'],
//            ['cost_main_id', 'compareCost'],
            ['year_serv', 'compareSpec'],
            [['time_serv_init', 'time_serv_spec_init'], 'date', 'format' => 'dd-MM-yyyy'],
        ];
    }

    /**
     * Проверка на одинаковость полей direction_id_main и direction_id_optional
     * @return  mixed
     */
    public function compareCost()
    {
        if (!$this->hasErrors()) {

            if ($this->direction_id_main == $this->direction_id_optional) {
                $this->addError('direction_id_main', Yii::t('art/teachers', 'The main activity may not be the same as the secondary one.'));
            }
        }
    }
     /**
     * Сравнение общего стажа со стажем по специальности
     * @return  mixed
     */
     public function compareSpec()
    {
        if (!$this->hasErrors()) {

            if ($this->year_serv < $this->year_serv_spec) {
                $this->addError('year_serv_spec', Yii::t('art/teachers', 'Experience in the specialty can not be more than the total experience.'));
            }
        }
    }
    /**
     * Преобразование даты в timestamp
     */
    public static function getDateToTimestamp($date) {
        return Yii::$app->formatter->asTimestamp($date);
    }

    /**
     * Преобразование timestamp в дату
     */
    public static function getTimestampToDate($mask = "php:d-m-Y", $timestamp) {
        return Yii::$app->formatter->asDate($timestamp, $mask);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'position_id' => Yii::t('art/teachers', 'Position ID'),
            'level_id' => Yii::t('art/teachers', 'Level ID'),
            'tab_num' => Yii::t('art/teachers', 'Tab Num'),
            'timestamp_serv' => Yii::t('art/teachers', 'Timestamp Serv'),
            'timestamp_serv_spec' => Yii::t('art/teachers', 'Timestamp Serv Spec'),
            'bonus_list' => Yii::t('art/teachers', 'Bonus List'),
            'year_serv' => Yii::t('art/teachers', 'Year Serv'),
            'year_serv_spec' => Yii::t('art/teachers', 'Year Serv Spec'),
            'teachersFullName' => Yii::t('art', 'Full Name'),
            'gridDepartmentSearch' => Yii::t('art/guide', 'Department'),
            'status' => Yii::t('art', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel()
    {
        return $this->hasOne(Level::className(), ['id' => 'level_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::className(), ['id' => 'position_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersDepartments()
    {
        return $this->hasMany(TeachersDepartment::className(), ['teachers_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersActivity()
    {
        return $this->hasMany(TeachersActivity::className(), ['teachers_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */

    public function getBonusItem()
    {
        return $this->hasMany(BonusItem::className(), ['id' => 'bonus_item_id'])
            ->viaTable('teachers_bonus', ['teachers_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getDepartmentItem()
    {
        return $this->hasMany(Department::className(), ['id' => 'department_id'])
            ->viaTable('teachers_department', ['teachers_id' => 'id']);
    }

    public static function getBonusItemList()
    {
        return ArrayHelper::map(BonusItem::find()
            ->innerJoin('teachers_bonus_category', 'teachers_bonus_category.id = teachers_bonus_item.bonus_category_id')
            ->andWhere(['teachers_bonus_item.status' => BonusItem::STATUS_ACTIVE])
            ->select('teachers_bonus_item.id as id, teachers_bonus_item.name as name, teachers_bonus_category.name as name_category')
            ->orderBy('teachers_bonus_item.bonus_category_id')
            ->addOrderBy('teachers_bonus_item.name')
            ->asArray()->all(), 'id', 'name', 'name_category');
    }

    public static function getDepartmentList()
    {
        return ArrayHelper::map(Department::find()
            ->innerJoin('division', 'division.id = department.division_id')
            ->andWhere(['department.status' => Department::STATUS_ACTIVE])
            ->select('department.id as id, department.name as name, division.name as name_category')
            ->orderBy('division.id')
            ->addOrderBy('department.name')
            ->asArray()->all(), 'id', 'name', 'name_category');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    } 
    /**
     * Геттер полного имени юзера
     */
    public function getTeachersFullName()
    {
        return $this->user->fullName;
    }
    /**
     * на 1 сентября текущего года  - в следующем году данные по стажу автоматически обновятся 
     * (в базе ничего не меняется)
     * хранится условная временная метка
     * 
     * @param type $year_serv
     * @param type $time_serv_init
     * @return type integer
     */
    public static function getTimestampServ($year_serv, $time_serv_init) {
             
        if ($year_serv != NULL)  return Teachers::getDateToTimestamp($time_serv_init) - $year_serv * Teachers::YEAR_SEC;
        return NULL;
        
    }
    /**
     * Метка времени текущего года выбранной даты
     * @param type $mon
     * @param type $day
     * @return type integer
     */
    public static function getThisTime()
    {
        $time = mktime(0, 0, 0, Teachers::SERV_MON, Teachers::SERV_DAY, date('Y', time()));
        $year = (date('m', time()) > Teachers::SERV_MON and $time > time()) ? date('Y', time()) : date('Y', time()) - 1;
        return mktime(0, 0, 0, Teachers::SERV_MON, Teachers::SERV_DAY, $year);
    }
    /**
     * Преобразование timуstamp в дату по маске
     * @return type string
     */
    public static function getTimeServInit() {
        
        $this_time = Teachers::getThisTime();
        
        return Teachers::getTimestampToDate("php:d-m-Y", $this_time);
        
    }
    /**
     * Формирует кол-во лет стажа
     * @param type $timestamp_serv
     * @return type float
     */
    public static function getYearServ($timestamp_serv) {
        
        $this_time = Teachers::getThisTime();
        
        if ($timestamp_serv != NULL)  return round(($this_time - $timestamp_serv) / Teachers::YEAR_SEC, 2);
        return NULL;
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
        );
    }
}
