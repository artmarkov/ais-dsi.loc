<?php

namespace backend\controllers\service;

use artsoft\helpers\Schedule;
use common\models\service\search\UsersAttendlogViewSearch;
use common\models\service\UsersAttendlog;
use common\models\service\UsersAttendlogView;
use common\models\service\UsersCard;
use Yii;
use artsoft\controllers\admin\BaseController;
use yii\base\DynamicModel;
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

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        if (!Yii::$app->request->get('user_common_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET user_common_id.");
        }

        return $this->renderIsAjax('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;


        return $this->renderIsAjax('update', compact('model'));
    }
    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionOver($id)
    {
        $model = $this->findModel($id);
        if ($model->overKey()) {
            Yii::$app->session->setFlash('info', 'Ключ успешно сдан.');
        } else {
            Yii::$app->session->setFlash('danger', 'Ошибка процедуры возврата ключа.');
        }
        return $this->redirect(Url::to('/admin/service/attendlog'));
    }

    public function actionFind()
    {
//        $week_day = Schedule::timestamp2WeekDay($timestamp);
//        $week_num = Schedule::timestamp2WeekNum($timestamp);

        $model = new DynamicModel(['user_common_id', 'key_hex', 'key_flag']);
        $model->addRule(['key_flag'], 'required')
            ->addRule(['user_common_id', 'key_flag'], 'integer')
            ->addRule(['key_hex'], 'string', ['min' => 8, 'max' => 8])
            ->addRule(['user_common_id', 'key_hex'], 'required', ['when' => function ($model) {
                return !$model->user_common_id && !$model->key_hex;
            }, 'whenClient' => 'function(){return false;}', 'message' => 'Данное поле является обязательным.']);
        $model->key_flag = 0;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->key_flag == 0) {
                $user_common_id = UsersCard::find()->select('user_common_id')->where(['=', 'key_hex', $model->key_hex])->scalar();
                if (!$user_common_id) {
                    Yii::$app->session->setFlash('warning', 'Пользователь по пропуску не найден.');
                }
            } else {
                $user_common_id = $model->user_common_id;
            }
            if ($user_common_id) {
                $timestamp = Schedule::getStartEndDay();

                $id = UsersAttendlog::find()
                    ->where(['user_common_id' => $user_common_id])
                    ->andWhere(['between', 'created_at', $timestamp[0], $timestamp[1]])->scalar();

                return $this->redirect($id ? Url::to(['update', 'id' => $id]) : Url::to(['create', 'user_common_id' => $user_common_id]));
            }
        }
        return $this->renderIsAjax('_find', ['model' => $model]);
    }
}