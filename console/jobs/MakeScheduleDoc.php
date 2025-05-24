<?php

namespace console\jobs;

use artsoft\helpers\ArtHelper;
use common\models\reports\ProgressReport;
use Yii;
use yii\base\DynamicModel;

/**
 * Class FlushCache.
 */
class MakeScheduleDoc extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $model_date = new DynamicModel(['plan_year', 'teachers_id', 'is_history']);
        $model_date->addRule(['plan_year', 'teachers_id',  'is_history'], 'integer');
        $model_date->plan_year = ArtHelper::getStudyYearDefault();
        $model_date->teachers_id = 1015;
        $model_date->is_history = true;
        $models = new ProgressReport($model_date);
        $models->saveXls();
        $models->makeDocument();
    }
}
