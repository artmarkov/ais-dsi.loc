<?php

namespace console\jobs;

use artsoft\helpers\ArtHelper;
use common\models\reports\ProgressReport;
use common\models\teachers\TeachersConsult;
use common\models\teachers\TeachersSchedule;
use common\models\user\UserCommon;
use Yii;
use yii\base\DynamicModel;
use yii\db\Query;

/**
 * Class FlushCache.
 */
class MakeScheduleDoc extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $teachersIds = (new Query())->from('teachers_view')
            ->select('teachers_id')
            ->where(['status' => UserCommon::STATUS_ACTIVE])
            ->column();
        $model_date = new DynamicModel(['plan_year', 'teachers_id']);
        $model_date->addRule(['plan_year', 'teachers_id'], 'integer');
        $model_date->plan_year = ArtHelper::getStudyYearDefault();
        foreach ($teachersIds as $item => $teachers_id) {
            $model_date->teachers_id = $teachers_id;
            $models = new TeachersSchedule($model_date);
            $models->saveXls();
            $models->makeDocument();
            $models = new TeachersConsult($model_date);
            $models->saveXls();
            $models->makeDocument();
        }
    }
}
