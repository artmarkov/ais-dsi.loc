<?php

namespace backend\controllers\venue;

use common\models\venue\VenueSity;
use common\models\venue\VenuePlace;
use common\models\venue\VenueDistrict;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\venue\VenuePlace model.
 */
class DefaultController extends MainController {

    public $modelClass = 'common\models\venue\VenuePlace';
    public $modelSearchClass = 'common\models\venue\search\VenuePlaceSearch';

    /**
     * 
     *  формируем список городов для widget DepDrop::classname()
     */
    public function actionSity() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = VenueSity::getSityByCountryId($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * 
     *  формируем список районов для widget DepDrop::classname()
     */
    public function actionDistrict() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $subcat_id = $parents[0];
                $out = VenueDistrict::getDistrictBySityId($subcat_id);


                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;


        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {

            if ($model->district_id == NULL) {
                $model->district_id = 0; // если нет в городе района присваиваем значение 0
            }
            if ($model->sity_id == NULL) {
                $model->sity_id = 0; // если нет в стране городов )) - присваиваем значение 0
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
                return $this->redirect($this->getRedirectPage('update', $model));
            }
        } else {
            return $this->renderIsAjax('create', compact('model'));
        }
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id) {
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {

            if ($model->district_id == NULL) {
                $model->district_id = 0; // если нет в городе района присваиваем значение 0
            }
            if ($model->sity_id == NULL) {
                $model->sity_id = 0; // если нет в стране городов )) - присваиваем значение 0
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
                return $this->redirect($this->getRedirectPage('update', $model));
            }
        } else {
            return $this->renderIsAjax('update', compact('model'));
        }
    }

}
