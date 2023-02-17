<?php

namespace backend\controllers\indivplan;

use common\models\teachers\search\TeachersPlanSearch;
use common\models\teachers\TeachersPlan;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\TeachersPlan model.
 * $model_date
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\teachers\TeachersPlan';
    public $modelSearchClass = 'common\models\teachers\search\TeachersPlanSearch';
    public $modelHistoryClass = 'common\models\history\TeachersPlanHistory';

    public function actionIndex()
    {
        $model_date = $this->modelDate;

        $query = TeachersPlan::find()->where(['=', 'plan_year', $model_date->plan_year]);

        $searchModel = new TeachersPlanSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date'));
    }

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = $this->modelDate;
        $model = new $this->modelClass;
        $model->plan_year = $model_date->plan_year;

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