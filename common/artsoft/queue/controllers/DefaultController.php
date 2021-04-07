<?php

namespace artsoft\queue\controllers;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * DefaultController implements the CRUD actions for artsoft\queue\models\QueueSchedule model.
 */
class DefaultController extends BaseController 
{
    public $modelClass       = 'artsoft\queue\models\QueueSchedule';
    public $modelSearchClass = 'artsoft\queue\models\search\QueueScheduleSearch';

    /**
     * @param int $id
     * @return mixed|string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->renderIsAjax($this->viewView, [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * 
     * @param type $id
     * @return type
     */
    public function actionRun($id) {
        if ($this->modelClass::runNow($id)) {
            Yii::$app->session->setFlash('success', Yii::t('art/queue', 'The job has been queued.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('art/queue', 'Error sending job to queue.'));
        }

        return $this->redirect($this->getRedirectPage('index', $this->modelClass));
    }

    /**
     * 
     * @return type
     */
    public function actionBulkRun() {
        if (Yii::$app->request->post('selection')) {

            $where = ['id' => Yii::$app->request->post('selection', [])];
            if ($this->modelClass::updateAll(['run_now' => $this->modelClass::RUN_NOW], $where)) {
                Yii::$app->session->setFlash('success', Yii::t('art/queue', 'All selected jobs has been queued.'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('art/queue', 'Error sending selected jobs to queue.'));
            }
            return $this->redirect($this->getRedirectPage('index', $this->modelClass));
        }
    }
    /**
     * 
     * @param type $id
     * @return type
     */
    public function actionActivate($id)
    {
       if ($this->modelClass::runActivate($id)) {
            Yii::$app->session->setFlash('success', Yii::t('art/queue', 'The schedule is successfully activated.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('art/queue', 'Schedule activation error.'));
        }

       return $this->redirect($this->getRedirectPage('index', $this->modelClass));
    }
    /**
     * 
     * @param type $id
     * @return type
     */
    public function actionDeactivate($id)
    {
       if ($this->modelClass::runDeactivate($id)) {
            Yii::$app->session->setFlash('success', Yii::t('art/queue', 'The schedule is successfully deactivated.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('art/queue', 'Schedule deactivation error.'));
        }

        return $this->redirect($this->getRedirectPage('index', $this->modelClass));
    }

}