<?php

/**
 * @link https://github.com/artmarkov/yii2-art-queue-schedule
 * @copyright Copyright (c) 2019 Artur Markov
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace artsoft\queue\traits;

use Yii;

/**
 * DateTimeTrait
 * @author Artur Markov <artmarkov@mail.ru> 
 */
trait DateTimeTrait {

    /**
     * @return type string
     */
    public function getCreatedDate()
    {
        return $this->getDate($this->created_at);
    }

    /**
     * @return type string
     */
    public function getCreatedTime()
    {
        return $this->getTime($this->created_at);
    }

    /**
     * @return type string
     */
    public function getCreatedDatetime()
    {
        return "{$this->createdDate} {$this->createdTime}";
    }

    /**
     * @return type string
     */
    public function getUpdatedDate()
    {
        return $this->getDate($this->updated_at);
    }

    /**
     * @return type string
     */
    public function getUpdatedTime()
    {
        return $this->getTime($this->updated_at);
    }

    /**
     * @return type string
     */
    public function getUpdatedDatetime()
    {
        return "{$this->updatedDate} {$this->updatedTime}";
    }

    /**
     * @return type string
     */
    public function getDate($timestamp) 
    {
        return Yii::$app->formatter->asDate(($this->isNewRecord) ? time() : $timestamp);
    }

    /**
     * @return type string
     */
    public function getTime($timestamp) 
    {
        return Yii::$app->formatter->asTime(($this->isNewRecord) ? time() : $timestamp);
    }
}
