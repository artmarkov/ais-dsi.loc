<?php

namespace backend\controllers\service;

use artsoft\helpers\Schedule;
use backend\models\Model;
use common\models\service\search\UsersAttendlogViewSearch;
use common\models\service\UsersAttendlog;
use common\models\service\UsersAttendlogKey;
use common\models\service\UsersAttendlogView;
use common\models\service\UsersCard;
use Yii;
use artsoft\controllers\admin\BaseController;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * AttendlogController implements the CRUD actions for common\models\service\UsersAttendlog model.
 */
class AttendlogController extends BaseController
{
    public $modelClass = 'common\models\service\UsersAttendlog';
    public  $modelSearchClass = 'common\models\service\search\UsersAttendlogViewSearch';

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model_date = new DynamicModel(['date']);
        $model_date->addRule(['date'], 'required')
            ->addRule(['date'], 'date');

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $timestamp = Schedule::getStartEndDay();
            $model_date->date = Yii::$app->formatter->asDate($timestamp[0]);
        }
        $timestamp = Yii::$app->formatter->asTimestamp($model_date->date);

        $query = UsersAttendlogView::find()->where(['=', 'timestamp', $timestamp]);
        $searchModel = new UsersAttendlogViewSearch($query);
        $params = $this->getParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date'));
    }

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        if (!Yii::$app->request->get('user_common_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET user_common_id.");
        }
        $timestamp = Schedule::getStartEndDay();
        $model = new $this->modelClass;
        $model->user_common_id = Yii::$app->request->get('user_common_id');
        $model->timestamp = $timestamp[0];
        $modelsDependency = [new UsersAttendlogKey()];

        if ($model->load(Yii::$app->request->post())) {

            $modelsDependency = Model::createMultiple(UsersAttendlogKey::class);
            Model::loadMultiple($modelsDependency, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsDependency) && $valid;
           //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {

                    if ($flag = $model->save(false)) {
                        foreach ($modelsDependency as $modelDependency) {
                            $modelDependency->users_attendlog_id = $model->id;
                            if (!($flag = $modelDependency->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->renderIsAjax('create', [
            'model' => $model,
            'modelsDependency' => (empty($modelsDependency)) ? [new UsersAttendlogKey] : $modelsDependency,
            'readonly' => false
        ]);
    }

    public function actionUpdate($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The CreativeWorks was not found.");
        }

        $modelsDependency = $model->userAttendlogKey;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsDependency, 'id', 'id');
            $modelsDependency = Model::createMultiple(UsersAttendlogKey::class, $modelsDependency);
            Model::loadMultiple($modelsDependency, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDependency, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsDependency) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            UsersAttendlogKey::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsDependency as $modelDependency) {
                            $modelDependency->users_attendlog_id = $model->id;
                            if (!($flag = $modelDependency->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
            'modelsDependency' => (empty($modelsDependency)) ? [new UsersAttendlogKey] : $modelsDependency,
            'readonly' => false
        ]);
    }
    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionOver($id)
    {
        $model = UsersAttendlogKey::findOne($id);
        if ($model->overKey()) {
            Yii::$app->session->setFlash('info', 'Ключ успешно сдан.');
        } else {
            Yii::$app->session->setFlash('danger', 'Ошибка процедуры возврата ключа.');
        }
        return $this->redirect('/admin/service/attendlog/index');
    }

    public function actionFind()
    {
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
                    ->andWhere(['=', 'timestamp',  $timestamp[0]])->scalar();

                return $this->redirect($id ? Url::to(['update', 'id' => $id]) : Url::to(['create', 'user_common_id' => $user_common_id]));
            }
        }
        return $this->renderIsAjax('_find', ['model' => $model]);
    }
}