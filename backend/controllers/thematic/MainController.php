<?php

namespace backend\controllers\thematic;

use Yii;
use yii\base\DynamicModel;

class MainController extends \backend\controllers\DefaultController
{
    public $modelDate;

    public $tabMenu = [
        ['label' => 'Тематические (репертуарные) планы',  'url' => ['/thematic/default/index']],
    ];


    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            $session = Yii::$app->session;

            $model = new DynamicModel(['plan_year','teachers_id']);
            $model->addRule(['plan_year', 'teachers_id'], 'required')->addRule(['plan_year'], 'integer')->addRule(['teachers_id'], 'integer');
            if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
                $model->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
                $model->teachers_id = $session->get('__backendTeachersId') ?? null;
            }
            $session->set('__backendPlanYear', $model->plan_year);
            $session->set('__backendTeachersId', $model->teachers_id);
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