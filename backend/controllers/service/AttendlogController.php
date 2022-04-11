<?php

namespace backend\controllers\service;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\Schedule;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use common\models\service\search\UsersAttendlogViewSearch;
use common\models\service\UsersAttendlogView;
use Yii;
use artsoft\controllers\admin\BaseController;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * AttendlogController implements the CRUD actions for common\models\service\UsersAttendlog model.
 */
class AttendlogController extends BaseController
{
    public $modelClass = 'common\models\service\UsersAttendlog';

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['date']);
        $model_date->addRule(['date'], 'required')
            ->addRule(['date'], 'date');


        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $timestamp = Schedule::getStartEndDay();
            $model_date->date = $session->get('_attendlog_date') ?? Yii::$app->formatter->asDate($timestamp[0], 'php:d.m.Y');
        }
        $session->set('_attendlog_date', $model_date->date);
        $timestamp = Schedule::getStartEndDay(Yii::$app->formatter->asTimestamp($model_date->date));

        $query = UsersAttendlogView::find()->where(['between', 'timestamp', $timestamp[0], $timestamp[1]]);
        $searchModel = new UsersAttendlogViewSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date'));
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionOwer($id)
    {
        $model = $this->findModel($id);
        $model->timestamp_over = Yii::$app->formatter->asDatetime(time());
        $model->save(false);

        Yii::$app->session->setFlash('info', 'Ключ успешно сдан.');
        return $this->redirect(Url::to('/admin/service/attendlog'));
    }

    public function actionFind($timestamp)
    {
        $week_day = Schedule::timestamp2WeekDay($timestamp);
        $week_num = Schedule::timestamp2WeekNum($timestamp);

//        $model = $this->findModel($id);

        return $this->redirect(Url::to('/admin/service/attendlog'));
    }
}