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

            $model = new DynamicModel(['date_in', 'date_out', 'subject_sect_studyplan_id', 'subject_key', 'plan_year', 'teachers_id', 'auditory_id', 'subject_type_id', 'activity_list', 'studyplan_id']);
            $model->addRule(['plan_year'], 'required')
                ->addRule(['plan_year', 'studyplan_id', 'teachers_id', 'auditory_id', 'subject_sect_studyplan_id'], 'integer')
                ->addRule(['date_in', 'date_out'], 'safe')
                ->addRule('subject_key', 'string');
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