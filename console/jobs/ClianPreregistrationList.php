<?php

namespace console\jobs;

use common\models\education\EntrantPreregistrations;
use common\models\studyplan\Studyplan;
use Yii;

/**
 * Class ClianPreregistrationList.
 */
class ClianPreregistrationList extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $models = EntrantPreregistrations::find()
            ->innerJoin('studyplan', 'studyplan.student_id = entrant_preregistrations.student_id AND studyplan.plan_year = entrant_preregistrations.plan_year')
            ->innerJoin('entrant_programm', 'entrant_programm.id = entrant_preregistrations.entrant_programm_id')
            ->where(['studyplan.status' => Studyplan::STATUS_INACTIVE])
            ->andWhere(['entrant_preregistrations.status' => EntrantPreregistrations::REG_STATUS_STUDENT])
            ->andWhere('studyplan.programm_id = entrant_programm.programm_id')
            ->andWhere('studyplan.course = entrant_programm.course')
            ->all();
        foreach ($models as $model) {
            $model->status = EntrantPreregistrations::REG_PLAN_CLOSED;
            $model->update(false);
        }
    }

}
