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

            $session = Yii::$app->session;

            $model = new DynamicModel(['plan_year']);
            $model->addRule(['plan_year'], 'required')->addRule(['plan_year'], 'integer');
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