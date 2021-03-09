<?php

namespace backend\controllers\subject;

use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\subject\Subject model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\subject\Subject';
    public $modelSearchClass = 'common\models\subject\search\SubjectSearch';

    public function actionUpdate($id) {

        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {

            //echo '<pre>' . print_r($model, true) . '</pre>';

            if ($model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($model);
            }
        } else {
            return $this->renderIsAjax('update', compact('model'));
        }
    }
}