<?php

namespace backend\controllers\teachers;

use common\models\teachers\Stake;
use common\models\teachers\Cost;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
use common\models\user\User;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\Teachers model.
 */
class DefaultController extends MainController
{

    public $modelClass = 'common\models\teachers\Teachers';
    public $modelSearchClass = 'common\models\teachers\search\TeachersSearch';

    /**
     * на 1 сентября текущего года  - в следующем году данные по стажу автоматически обновятся 
     * (в базе ничего не меняется)
     * хранится условная временная метка
     */
    public function actionCreate() {

        $model = new $this->modelClass;
        $modelUser = new UserCommon();
        
        $model->time_serv_init = Teachers::getTimeServInit();
        $model->time_serv_spec_init = Teachers::getTimeServInit();
       
        if ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model,$modelUser);
        } elseif ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            //echo '<pre>' . print_r($model, true) . '</pre>';

                $modelUser->user_category = User::USER_CATEGORY_TEACHER;
                $modelUser->status = User::STATUS_INACTIVE;                
            
                $model->cost_main_id = Cost::getCostId($model->direction_id_main, $model->stake_id_main);            
                $model->cost_optional_id = Cost::getCostId($model->direction_id_optional, $model->stake_id_optional);
                
                $model->timestamp_serv = Teachers::getTimestampServ($model->year_serv, $model->time_serv_init);
                $model->timestamp_serv_spec = Teachers::getTimestampServ($model->year_serv_spec, $model->time_serv_spec_init);
            
            if ($modelUser->save()) {
                $model->user_id = $modelUser->id;               
                
                if ($model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
                return $this->redirect($this->getRedirectPage('update', $model));
                }
            }
        } else {
            return $this->renderIsAjax('create', [
            'modelUser' => $modelUser,
            'model' => $model,
        ]);
        }
    }
    
     public function actionUpdate($id) {

        $model = $this->findModel($id);
        $modelUser = UserCommon::findOne(['id' => $model->user_id, 'user_category' => User::USER_CATEGORY_TEACHER]);
        
        if (!isset($model, $modelUser)) {
            throw new NotFoundHttpException("The user was not found.");
        }

            $model->direction_id_main = Cost::getDirectionId($model->cost_main_id);        
            $model->stake_id_main = Cost::getStakeId($model->cost_main_id);
       
            $model->direction_id_optional = Cost::getDirectionId($model->cost_optional_id);        
            $model->stake_id_optional = Cost::getStakeId($model->cost_optional_id);

            $model->time_serv_init = Teachers::getTimeServInit();
            $model->time_serv_spec_init = Teachers::getTimeServInit();
       
            $model->year_serv = Teachers::getYearServ($model->timestamp_serv);        
            $model->year_serv_spec = Teachers::getYearServ($model->timestamp_serv_spec);

        if ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model,$modelUser);
        } elseif ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            //echo '<pre>' . print_r($model, true) . '</pre>';

            $model->cost_main_id = Cost::getCostId($model->direction_id_main, $model->stake_id_main);           
            $model->cost_optional_id = Cost::getCostId($model->direction_id_optional, $model->stake_id_optional);

            $model->timestamp_serv = Teachers::getTimestampServ($model->year_serv, $model->time_serv_init);
            $model->timestamp_serv_spec = Teachers::getTimestampServ($model->year_serv_spec, $model->time_serv_spec_init);
            
            if ($modelUser->save() && $model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
                return $this->redirect($this->getRedirectPage('update', $model));
            }
        } else {
            return $this->renderIsAjax('update', [
            'modelUser' => $modelUser,
            'model' => $model,
        ]);
        }
    }

    /**
     *
     *  формируем список ставок для widget DepDrop::classname()
     */
    public function actionStake() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $direction_id = $parents[0];
                $out = Stake::getStakeByDirectionId($direction_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

}
