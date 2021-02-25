<?php

namespace artsoft\queue\models;

use yii\db\ActiveRecord;
use artsoft\queue\Env;
use Cron\CronExpression;
use Yii;

/**
 * Push Record
 */
class QueueScheduleMain extends ActiveRecord
{
     
    const STATUS_DISABLE = 0;    // закрытый
    const STATUS_ENABLE = 1;      // активный 
    
    const RUN_NOW = 1;          // выполнить немедленно
    const RUN_STOP = 0;         // закрыть немедленное выполнение
    
    const EXEC_DELAY = 0;       // задержка перед исполнением
    
    /**
     * {@inheritdoc}
     * @return \artsoft\queue\models\query\QueueScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QueueScheduleQuery(get_called_class());
    }
    
   /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Env::ensure()->scheduleTableName;
    }
    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function runNow($id)
    {
        $model = self::find()->byId($id)->stop()->one();
        if (!$model)
        {
            return false;
        }
        $model->run_now = self::RUN_NOW;
        if ($model->save(false))
        {
            return true;
        }
        return false;
    }
    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function runActivate($id)
    {
        $model = self::find()->byId($id)->one();
        if (!$model)
        {
            return false;
        }
        $model->status  = self::STATUS_ENABLE;
        if ($model->save(false))
        {
            return true;
        }
        return false;
    }    
    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function runDeactivate($id)
    {
        $model = self::find()->byId($id)->one();
        if (!$model)
        {
            return false;
        }
        $model->status  = self::STATUS_DISABLE;
        if ($model->save(false))
        {
            return true;
        }
        return false;
    }    
     /**
     * 
     * @param type $cron_expression
     * @return type
     */
    public static function runCron($cron_expression) {
        $cron = CronExpression::factory($cron_expression);
        $cron->isDue('now', Yii::$app->formatter->timeZone);
        return $cron;
    }
    
    /**
     * 
     * @param type $cron
     * @return type
     */
    public static function cronNextDate($cron) {
        return $cron->getNextRunDate('now', 0, false, Yii::$app->formatter->timeZone)->format('U');
    }
    /**
     * 
     * @param type $cron
     * @param int $qty
     * @return int array
     */
    public static function cronNextDates($cron, int $qty = 10) {       
        return $cron->getMultipleRunDates($qty, 'now', false, false, Yii::$app->formatter->timeZone);
    }
}

