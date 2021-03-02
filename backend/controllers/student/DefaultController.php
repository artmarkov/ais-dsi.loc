<?php

namespace backend\controllers\student;

use common\models\user\User;
use common\models\user\UserCommon;
use yii\web\NotFoundHttpException;
use Yii;


/**
 * DefaultController implements the CRUD actions for common\models\student\Student model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\student\Student';
    public $modelSearchClass = 'common\models\student\search\StudentSearch';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;
        $modelUser = new UserCommon();
        $model->setAttributes(
            [
                'sertificate_name' => 'Свидетельство о рождении',
            ]
        );
        if ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model, $modelUser);
        } elseif ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            //echo '<pre>' . print_r($model, true) . '</pre>';

            $modelUser->user_category = User::USER_CATEGORY_STUDENT;
            $modelUser->status = User::STATUS_INACTIVE;

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

    public function actionUpdate($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);
        $modelUser = UserCommon::findOne(['id' => $model->user_id, 'user_category' => User::USER_CATEGORY_STUDENT]);

        if (!isset($model, $modelUser)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        if ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model, $modelUser);
        } elseif ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            //echo '<pre>' . print_r($model, true) . '</pre>';

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
     * Удаляет связь студент - родитель
     * Элемент Родитель при это не удаляется
     */
    public function actionRemove()
    {
        $id = Yii::$app->request->get('id');
        $model = \common\models\user\UserFamily::findOne($id);
        if (empty($model)) return false;
        $model->delete();
        Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect(Yii::$app->request->referrer);
    }
}