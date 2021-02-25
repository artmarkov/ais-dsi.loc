<?php

namespace artsoft\queue;

use yii\base\Behavior;
use yii\queue\PushEvent;
use yii\queue\JobInterface;
use yii\queue\Queue;
use artsoft\queue\Env;
use artsoft\queue\models\QueueScheduleMain;

/**
 * Description of QueueScheduleBehavior
 *
 * @author artmarkov
 */
class JobSchedule extends Behavior {

    /**
     * @var array of job class names that this behavior should tracks.
     * @since 0.3.2
     */
    public $only = [];
    /**
     * @var array of job class names that this behavior should not tracks.
     * @since 0.3.2
     */
    public $except = [];
     /**
     * @var Env
     */
    protected $env;
    
    /**
     * @param Env $env
     * @param array $config
     */
    public function __construct(Env $env, $config = []) {
        $this->env = $env;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            Queue::EVENT_BEFORE_PUSH => 'beforePush',
        ];
    }

    /**
     * 
     * @param PushEvent $event
     * @return type
     */
    public function beforePush(PushEvent $event) {

        if (!$this->isActive($event->job)) {
            return;
        }
        if ($this->env->db->getTransaction()) {
            // create new database connection, if there is an open transaction
            // to ensure insert statement is not affected by a rollback
            $this->env->db = clone $this->env->db;
        }
        $schedule = $this->getScheduleRecord($event);
        
        if (!$schedule) {
            $event->handled = true;
            return;
        }
        
        $event->priority = $schedule->priority;
        $event->delay = QueueScheduleMain::EXEC_DELAY;
        
        if (!$this->isRunNow($schedule) && !$this->isRunSchedule($schedule)) {
            $event->handled = true;
        }        
        $cron = QueueScheduleMain::runCron($schedule->cron_expression);
        $schedule->run_now = QueueScheduleMain::RUN_STOP;
        $schedule->next_date = QueueScheduleMain::cronNextDate($cron);
        $schedule->save(false);
    }

    /**
     * 
     * @param PushEvent $event
     * @return type
     */
    protected function getScheduleRecord(PushEvent $event) {
        return $this->env->db->useMaster(function () use ($event) {
                    return QueueScheduleMain::find()
                                    ->where(['class' => get_class($event->job)])
                                    ->one();
                });
    }
    
    /**
     * 
     * @param type $schedule
     * @return boolean
     */
    protected function isRunNow($schedule) {
        if ($schedule->run_now == QueueScheduleMain::RUN_NOW) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return boolean
     */
    protected function isRunSchedule($schedule) {
        if ($schedule->status == QueueScheduleMain::STATUS_ENABLE && $schedule->next_date == $this->getQueueTimestamp()) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return type int
     */
    protected function getQueueTimestamp() {
        return mktime(date("H"), date("i"), 0, date("n"), date("j"), date("Y"));
    }
    
    /**
     * @param JobInterface $job
     * @return bool
     * @since 0.3.2
     */
    protected function isActive(JobInterface $job)
    {
        $onlyMatch = true;
        if ($this->only) {
            $onlyMatch = false;
            foreach ($this->only as $className) {
                if (is_a($job, $className)) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        $exceptMatch = false;
        foreach ($this->except as $className) {
            if (is_a($job, $className)) {
                $exceptMatch = true;
                break;
            }
        }

        return !$exceptMatch && $onlyMatch;
    }
}
