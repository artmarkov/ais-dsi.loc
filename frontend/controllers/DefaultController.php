<?php
namespace frontend\controllers;

use artsoft\controllers\admin\BaseController;
use Yii;
use yii\base\DynamicModel;

class DefaultController extends BaseController {

    public $layout = '@frontend/views/layouts/main.php';

    public $modelDate;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            ini_set('memory_limit', '2024M');
            $session = Yii::$app->session;

            $model = new DynamicModel(['plan_year', 'teachers_id', 'auditory_id', 'studyplan_id', 'subject_sect_studyplan_id']);
            $model->addRule(['plan_year'], 'required')
                ->addRule(['plan_year', 'teachers_id', 'auditory_id', 'studyplan_id'], 'integer');
            if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
                $model->plan_year = $session->get('__frontendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
            }
            $session->set('__frontendPlanYear', $model->plan_year);
            $this->modelDate = $model;
            return true;
        }
        return false;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        $session = Yii::$app->session;
        $session->close();
        return $result;
    }


}