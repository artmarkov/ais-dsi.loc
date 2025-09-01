<?php

namespace console\controllers;

use artsoft\helpers\ArtHelper;
use common\models\reports\ProgressReport;
use Yii;
use yii\base\DynamicModel;
use yii\console\Controller;
use artsoft\helpers\Html;
use common\models\planfix\Planfix;
use common\models\user\UsersView;
use yii\helpers\Console;

/**
 * Description of ObjectController
 *
 * run  console command:  php yii test
 *
 * @author markov-av
 */
class TestController extends Controller
{
    public function actionIndex()
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
