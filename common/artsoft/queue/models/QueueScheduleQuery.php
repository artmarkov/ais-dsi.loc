<?php

namespace artsoft\queue\models;

/**
 * This is the ActiveQuery class for [[\artsoft\queue\models\QueueSchedule]].
 *
 * @see \artsoft\queue\models\QueueSchedule
 */

use yii\db\ActiveQuery;

class QueueScheduleQuery extends ActiveQuery
{
    
    /**
     * {@inheritdoc}
     * @return \artsoft\queue\models\QueueSchedule[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \artsoft\queue\models\QueueSchedule|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    /**
     * @param int $id
     * @return $this
     */
    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }
    /**
     * 
     * @return type
     */
    public function stop()
    {
        return $this->andWhere(['run_now' => QueueScheduleMain::RUN_STOP]);
    }
}
