<?php

namespace backend\controllers\efficiency;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use common\models\efficiency\EfficiencyTree;
use common\models\efficiency\TeachersEfficiency;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;


/**
 * DefaultController implements the CRUD actions for common\models\efficiency\Efficiency model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\efficiency\TeachersEfficiency';
    public $modelSearchClass = 'common\models\efficiency\search\TeachersEfficiencySearch';
    public $modelHistoryClass = 'common\models\history\EfficiencyHistory';

    /**
     * @return mixed|string|\yii\web\Response
     */
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies')];

        $modelClass = $this->modelClass;
        $searchModel = $this->modelSearchClass ? new $this->modelSearchClass : null;
        $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
            && !User::hasPermission($modelClass::getFullAccessPermission()));

        if ($searchModel) {
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $dataProvider = $searchModel->search($params);
        } else {
            $restrictParams = ($restrictAccess) ? [$modelClass::getOwnerField() => Yii::$app->user->identity->id] : [];
            $dataProvider = new ActiveDataProvider(['query' => $modelClass::find()->where($restrictParams)]);
        }

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }

    /**
     * @param $id
     * @param $timestamp_in
     * @param $timestamp_out
     * @return string|\yii\web\Response
     */
    public function actionDetails($id, $timestamp_in, $timestamp_out)
    {
        $user = \common\models\teachers\Teachers::findOne($id)->getFullName();

        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => 'Сводная таблица', 'url' => ['/efficiency/default/summary']];
        $this->view->params['breadcrumbs'][] = $user;
        $searchModel = null;

        $dataProvider = new ActiveDataProvider(['query' => TeachersEfficiency::find()
            ->where(['between', 'date_in', $timestamp_in, $timestamp_out])
            ->andWhere(['=', 'teachers_id', $id]), 'pagination' => false
        ]);

        return $this->renderIsAjax($this->indexView, compact(['dataProvider', 'searchModel', 'id']));
    }

    /**
     * @param null $id
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate($id = null)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $id ? $model->teachers_id = [$id] : null;
        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    foreach ($model->teachers_id as $id => $teachers_id) {
                        $m = new $this->modelClass;
                        $m->teachers_id = $teachers_id;
                        $m->efficiency_id = $model->efficiency_id;
                        $m->bonus_vid_id = $model->bonus_vid_id;
                        $m->bonus = $model->bonus;
                        $m->date_in = $model->date_in;
                        if (!($flag = $m->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                        $this->redirect($this->getRedirectPage('index'));
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax($this->createView, [
            'model' => $model,
            'modelDependence' => $model,
            'readonly' => false
        ]);
    }

    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->updateView, [
            'model' => $model,
            'modelDependence' => $model,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    /**
     * @return mixed
     */
    public function actionSelect()
    {
        $id = \Yii::$app->request->post('id');
        $model = EfficiencyTree::findOne(['id' => $id]);

        return json_encode(['id' => $model->bonus_vid_id, 'value' => $model->value_default]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSummary($view = 'summary')
    {
        $session = Yii::$app->session;

        $this->view->params['tabMenu'] = $this->tabMenu;
        $day_in = Yii::$app->settings->get('module.day_in', 21);
        $day_out = Yii::$app->settings->get('module.day_out', 20);

        $model_date = new DynamicModel(['date_in', 'date_out', 'hidden_flag']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'date')
            ->addRule('hidden_flag', 'integer');

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $d = date('d');
            $m = date('m');
            $y = date('Y');

            $mon = $d > $day_in ? ($m == 12 ? 1 : $m + 1) : $m;
            $year = $m == 12 ? $y + 1 : $y;

            $model_date->date_in = $session->get('_efficiency_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, ($mon - 1), $day_in, $year), 'php:d.m.Y');
            $model_date->date_out = $session->get('_efficiency_date_out') ?? Yii::$app->formatter->asDate(mktime(23, 59, 59, $mon, $day_out, $year), 'php:d.m.Y');
            $model_date->hidden_flag = $session->get('_efficiency_hidden_flag') ?? 0;
        }
        $session->set('_efficiency_date_in', $model_date->date_in);
        $session->set('_efficiency_date_out', $model_date->date_out);
        $session->set('_efficiency_hidden_flag', $model_date->hidden_flag);

        $data = TeachersEfficiency::getSummaryData($model_date);

        if (Yii::$app->request->post('submitAction') == 'excel') {
            TeachersEfficiency::sendXlsx($data);
        }

        return $this->renderIsAjax($view, compact(['data', 'model_date']));
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBar()
    {
        return $this->actionSummary('bar');
    }

    /**
     * @param $id
     * @param $timestamp_in
     * @param $timestamp_out
     * @return string|\yii\web\Response
     */
    public function actionUserBar($id, $timestamp_in, $timestamp_out)
    {
        $user = \common\models\teachers\Teachers::findOne($id)->getFullName();

        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => 'Сводная таблица', 'url' => ['/efficiency/default/summary']];
        $this->view->params['breadcrumbs'][] = $user;
        
        return $this->renderIsAjax('user-bar', [
            'id' => $id,
            'timestamp_in' => $timestamp_in,
            'timestamp_out' => $timestamp_out,
        ]);
    }

}
