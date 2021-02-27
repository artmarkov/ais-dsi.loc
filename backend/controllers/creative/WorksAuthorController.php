<?php

namespace backend\controllers\creative;

use Yii;
use backend\controllers\DefaultController;
use common\models\creative\CreativeWorksAuthor;
use common\models\user\UserCommon;

/**
 * WorksAuthorController implements the CRUD actions for common\models\creative\CreativeWorksAuthor model.
 */
class WorksAuthorController extends DefaultController 
{
    public $modelClass       = 'common\models\creative\CreativeWorksAuthor';
    public $modelSearchClass = '';

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
    /**
     * Вызывается методом Ajax из worksAuthor.php
     */
    public function actionInitAuthor()
    {
        $id = Yii::$app->request->get('id');
        $author_id = Yii::$app->request->get('author_id');
        $model = new CreativeWorksAuthor();
        $modelUser = UserCommon::findOne($author_id);
        if(empty($modelUser)) return false;

        //echo '<pre>' . print_r($modelUser, true) . '</pre>';

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $model->works_id = $id;
        $model->author_id = $author_id;

        $this->layout = false;
        return $this->renderIsAjax('works-author-modal',  ['model' => $model, 'modelUser' => $modelUser]);
    }

    /**
     * @return array|\yii\web\Response
     * @throws HttpException
     */
    public function actionCreateAuthor() {

        $model = new CreativeWorksAuthor();

        if ($model->load(Yii::$app->request->post())) {

            // echo '<pre>' . print_r($model, true) . '</pre>';

            if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                return \yii\widgets\ActiveForm::validate($model);
            } elseif ($model->load(Yii::$app->request->post())) {
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been created.'));
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        } else {
            throw new HttpException(404, 'Page not found');
        }
    }

    /**
     * @return array|bool|string|\yii\web\Response
     */
    public function actionUpdateAuthor() {

        $id = Yii::$app->request->get('id');

        $model = CreativeWorksAuthor::findOne($id);
        $modelUser = UserCommon::findOne($model->author_id);

        if(empty($model)) return false;

            // echo '<pre>' . print_r($model, true) . '</pre>';

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \yii\widgets\ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {
            
            if ($model->save()) {
                Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been updated.'));
                return $this->redirect(Yii::$app->request->referrer);
            }

    } else {
        $this->layout = false;
        return $this->renderIsAjax('works-author-modal',  ['model' => $model, 'modelUser' => $modelUser]);
    }
}

    /**
     * @return bool|\yii\web\Response
     *
     */
    public function actionRemove() {
        $id = Yii::$app->request->get('id');        
        $model = CreativeWorksAuthor::findOne($id);
        if (empty($model)) return false;
        $model->delete();
        Yii::$app->session->setFlash('crudMessage', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect(Yii::$app->request->referrer); 
    }
}