<?php

namespace backend\controllers\reports;

use common\models\service\search\WorkingTimeLogSearch;
use common\models\service\WorkingTimeLog;
use Yii;
use yii\base\DynamicModel;

class WorkingTimeController extends \backend\controllers\DefaultController
{

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['tabMenu'] = $this->getMenu();
        $model_date = new DynamicModel(['date']);
        $model_date->addRule(['date'], 'required')
            ->addRule(['date'], 'date', ['format' => 'php:d.m.Y']);
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $day = date('d');
            $mon = date('m');
            $year = date('Y');

            $model_date->date = $session->get('_working_time_date') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, $day, $year) - 86400, 'php:d.m.Y');
        }
        $session->set('_working_time_date', $model_date->date);

        $query = WorkingTimeLog::find()->where(['date' => Yii::$app->formatter->asDate($model_date->date, 'php:Y-m-d')]);
        $searchModel = new WorkingTimeLogSearch($query);
        $params = $this->getParams();

        $dataProvider = $searchModel->search($params);
        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date'));
    }

    /**
     * @param string $view
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionSummary($view = 'summary')
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['tabMenu'] = $this->getMenu();
        $model_date = new DynamicModel(['date_in']);
        $model_date->addRule(['date_in'], 'required')
            ->addRule(['date_in'], 'date', ['format' => 'php:m.Y']);
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_working_time_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
        }
        $session->set('_working_time_date_in', $model_date->date_in);

        $data = WorkingTimeLog::getSummaryData($model_date);

        if (Yii::$app->request->post('submitAction') == 'excel') {
            WorkingTimeLog::sendXlsx($data);
        }

        return $this->renderIsAjax($view, compact(['data', 'model_date']));
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionBar()
    {
        return $this->actionSummary('bar');
    }

    /**
     * @param $id
     * @return array
     * @throws \yii\web\NotFoundHttpException
     */
    public function getMenu()
    {
        return [
            ['label' => 'Жкрнал посещаемости', 'url' => ['/reports/working-time/index']],
            ['label' => 'Сводная таблица',  'url' => ['/reports/working-time/summary']],
            ['label' => 'График',  'url' => ['/reports/working-time/bar']],
        ];
    }

}