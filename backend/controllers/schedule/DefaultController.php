<?php

namespace backend\controllers\schedule;

use artsoft\helpers\RefBook;
use common\models\auditory\Auditory;
use common\models\schedule\SubjectScheduleView;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersPlan;
use Yii;
use yii\helpers\ArrayHelper;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\schedule\SubjectScheduleView';
    public $modelSearchClass = 'common\models\schedule\search\SubjectScheduleViewSearch';


    public function actionIndex()
    {
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $model_date = $this->modelDate;

        $models = SubjectScheduleView::find()
            ->where(['=', 'plan_year', $model_date->plan_year])
            ->andWhere(['=', 'status', 1])
            ->andWhere(['IS NOT', 'auditory_id', null]);
        if ($model_date->teachers_id) {
            $models = $models->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($model_date->teachers_id)]);
        }
        $models = $models->asArray()->orderBy('week_day,time_in')->all();
        $modelsAuditory = RefBook::find('auditory_memo_1', 1)->getList();

        $modelsPlan = TeachersPlan::find()
            ->where(['=', 'plan_year', $model_date->plan_year]);
        if ($model_date->teachers_id) {
            $modelsPlan = $modelsPlan->where(['=', 'teachers_id', $model_date->teachers_id]);
        }
        $modelsPlan = $modelsPlan->asArray()->orderBy('week_day,time_plan_in')->all();

        $data = ArrayHelper::index($models, null, ['auditory_id', 'week_day']);
//        echo '<pre>' . print_r($modelsPlan, true) . '</pre>';
//        echo '<pre>' . print_r($data, true) . '</pre>'; die();
        return $this->renderIsAjax('index', compact('model_date', 'data', 'modelsAuditory'));

    }


}