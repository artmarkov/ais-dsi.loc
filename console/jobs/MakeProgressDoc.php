<?php

namespace console\jobs;

use artsoft\helpers\ArtHelper;
use common\models\reports\ProgressReport;
use common\models\user\UserCommon;
use yii\base\DynamicModel;
use yii\db\Query;

/**
 * Class MakeProgressDoc.
 */
class MakeProgressDoc extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
//        $teachersIds = (new Query())->from('teachers_view')
//            ->select('teachers_id')
//            ->where(['status' => UserCommon::STATUS_ACTIVE])
//            ->column();
        $model_date = new DynamicModel(['plan_year', 'teachers_id', 'is_history']);
        $model_date->addRule(['plan_year', 'teachers_id',  'is_history'], 'integer');
        $model_date->plan_year = ArtHelper::getStudyYearDefault();
        $model_date->teachers_id = 1015;
        $model_date->is_history = true;
//        foreach ($teachersIds as $item => $teachers_id) {
//            $model_date->teachers_id = $teachers_id;
            $models = new ProgressReport($model_date);
            $models->saveXls();
            $models->makeDocument();
            $models->cliarTemp();
        }
//    }
}
