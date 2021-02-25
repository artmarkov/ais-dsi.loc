<?php

namespace artsoft\queue;

use Yii;
use yii\base\BaseObject;
use yii\db\Connection;
use yii\di\Instance;

/**
 * Class Env
 */
class Env extends BaseObject {
    
    /**
     * @var Connection|array|string
     */
    public $db = 'db';

    /**
     * @var string
     */
    public $scheduleTableName = '{{%queue_schedule}}';
    
    /**
     * @return static
     */
    public static function ensure() {
        return Yii::$container->get(static::class);
    }

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
    }

}
