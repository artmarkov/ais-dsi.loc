<?php

namespace backend\controllers\indivplan;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\TeachersPlan model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\teachers\TeachersPlan';
    public $modelSearchClass = 'common\models\teachers\search\TeachersPlanSearch';
    public $modelHistoryClass = 'common\models\history\TeachersPlanHistory';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($model);
            }
        }

        return $this->renderIsAjax($this->createView, [
            'model' => $model,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id, $readonly = false)
    {
//        $this->view->params['tabMenu'] = $this->getMenu($id);
        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The SubjectSect was not found.");
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->render($this->updateView, [
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

    /**
     * @param int $id
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

}