<?php
namespace backend\controllers;

use common\models\teachers\Teachers;
use Yii;
use yii\base\DynamicModel;

class DefaultController  extends \artsoft\controllers\admin\BaseController {

    public $layout = '@artsoft/views/layouts/admin/main.php';

    public $modelDate;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            $session = Yii::$app->session;

            $model = new DynamicModel(['plan_year','teachers_id']);
            $model->addRule(['plan_year'], 'required')->addRule(['plan_year'], 'integer')->addRule(['teachers_id'], 'integer');
            if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
                $model->plan_year = $session->get('__backendPlanYear') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
            }
            $session->set('__backendPlanYear', $model->plan_year);
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

    public function debug($arr){
        echo '<pre>' . print_r($arr, true) . '</pre>';
    }
}