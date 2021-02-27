<?php

namespace backend\controllers\subject;

use Yii;
use common\models\service\Department;
/**
 * DefaultController implements the CRUD actions for common\models\subject\Subject model.
 */
class DefaultController extends \backend\controllers\DefaultController
{
    public $modelClass       = 'common\models\subject\Subject';
    public $modelSearchClass = 'common\models\subject\search\SubjectSearch';

    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            case 'create':
                return ['update', 'id' => $model->id];
                break;
            default:
                return parent::getRedirectPage($action, $model);
        }
    }

    public function actionUpdate($id) {

        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {

            //echo '<pre>' . print_r($model, true) . '</pre>';

            if ($model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
                return $this->redirect($this->getRedirectPage('update', $model));
            }
        } else {
            return $this->renderIsAjax('update', compact('model'));
        }
    }
}