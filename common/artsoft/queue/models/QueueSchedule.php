<?php

namespace artsoft\queue\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\queue\traits\DateTimeTrait;
use artsoft\queue\models\QueueScheduleMain;
use artsoft\models\User;

/**
 * This is the model class for table "{{%queue_schedule}}".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $class
 * @property string $status 
 * @property string $cron_expression 
 * @property int $priority
 * @property int $run_now
 * @property int $next_date
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property QueueSchedulePush[] $queue-schedulePushes
 */
class QueueSchedule extends QueueScheduleMain
{
    use DateTimeTrait;   
    
    const PRIORITY_HIGH = 1024; // высокий
    const PRIORITY_MED = 512;   // средний
    const PRIORITY_LOW = 1;     // низкий   
    
    public $nextDates;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),            
            BlameableBehavior::className(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'class', 'cron_expression'], 'required'],
            ['class', 'unique'],
            ['cron_expression', 'trim'],
            [['content'], 'string'],
            [['priority', 'run_now', 'next_date', 'status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_DISABLE],
            [['title', 'class', 'cron_expression'], 'string', 'max' => 127],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
            ['cron_expression', 'match', 'pattern' => Yii::$app->getModule('queue-schedule')->cronRedexp, 'message' => Yii::t('art/queue', 'Invalid cron expression.')],          
            ['created_by', 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            ['updated_by', 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'title' => Yii::t('art', 'Title'),
            'content' => Yii::t('art', 'Content'),
            'class' => Yii::t('art/queue', 'Job Class'),
            'status' => Yii::t('art', 'Status'),
            'cron_expression' => Yii::t('art/queue', 'Cron Expression'),
            'priority' => Yii::t('art/queue', 'Priority'),
            'next_date' => Yii::t('art/queue', 'Next Date'),
            'nextDates' => Yii::t('art/queue', 'Next Dates'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
    }

    /**
     * @return type string
     */
    public function getNextDate()
    {
        return $this->getDate($this->next_date);
    }
    /**
     * @return type array
     */
    public function getNextDates()
    {      
        $cron = QueueSchedule::runCron($this->cron_expression);        
        return $this->cronNextDates($cron);
    }
    
    /**
     * @return type string
     */
    public function getNextTime()
    {
        return $this->getTime($this->next_date);
    }
    /**
     * @return type string
     */
    public function getNextDatetime()
    {
        return "{$this->nextDate} {$this->nextTime}";
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    } /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList() {
        return array(
            self::STATUS_DISABLE => Yii::t('art/queue', 'Disable'),
            self::STATUS_ENABLE => Yii::t('art/queue', 'Enable'),
        );
    }

    /**
     * getStatusOptionsList
     * @return array
     */
     public static function getStatusOptionsList() {
        return [
            [self::STATUS_DISABLE, Yii::t('art/queue', 'Disable'), 'danger'],
            [self::STATUS_ENABLE, Yii::t('art/queue', 'Enable'), 'success']
        ];
    }  
    
    /**
     * getPriorityList
     * @return array
     */
    public static function getPriorityList() {
        return array(
            self::PRIORITY_HIGH => Yii::t('art/queue', 'High'),
            self::PRIORITY_MED => Yii::t('art/queue', 'Med'),
            self::PRIORITY_LOW => Yii::t('art/queue', 'Low'),
        );
    }

    /**
     * getPriorityOptionsList
     * @return array
     */
     public static function getPriorityOptionsList() {
        return [
            [self::PRIORITY_HIGH, Yii::t('art/queue', 'High'), 'info'],
            [self::PRIORITY_MED, Yii::t('art/queue', 'Med'), 'warning'],
            [self::PRIORITY_LOW, Yii::t('art/queue', 'Low'), 'default']
        ];
    }     
   
}