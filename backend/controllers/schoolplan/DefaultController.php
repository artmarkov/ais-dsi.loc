<?php

namespace backend\controllers\schoolplan;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use backend\models\Model;
use common\models\efficiency\search\TeachersEfficiencySearch;
use common\models\efficiency\TeachersEfficiency;
use common\models\guidesys\GuidePlanTree;
use common\models\history\EfficiencyHistory;
use common\models\history\SchoolplanProtocolHistory;
use common\models\schoolplan\SchoolplanProtocol;
use common\models\schoolplan\SchoolplanProtocolItems;
use common\models\schoolplan\SchoolplanView;
use common\models\schoolplan\search\SchoolplanProtocolSearch;
use common\models\schoolplan\search\SchoolplanViewSearch;
use common\models\teachers\search\TeachersLoadViewSearch;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadView;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * DefaultController implements the CRUD actions for common\models\schoolplan\Schoolplan model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\schoolplan\Schoolplan';
    public $modelSearchClass = 'common\models\schoolplan\search\SchoolplanViewSearch';
    public $modelHistoryClass = 'common\models\history\SchoolplanHistory';

    public function actionIndex()
    { $session = Yii::$app->session;

        $day_in = 1;
        $day_out = date("t");

        $model_date = new DynamicModel(['date_in', 'date_out']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'date');

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_schoolplan_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, $day_in, $year), 'php:d.m.Y');
            $model_date->date_out = $session->get('_schoolplan_date_out') ?? Yii::$app->formatter->asDate(mktime(23, 59, 59, $mon, $day_out, $year), 'php:d.m.Y');
        }
        $session->set('_schoolplan_date_in', $model_date->date_in);
        $session->set('_schoolplan_date_out', $model_date->date_out);

        $this->view->params['tabMenu'] = $this->tabMenu;

        $query = SchoolplanView::find()->where(['between', 'datetime_in', Yii::$app->formatter->asTimestamp($model_date->date_in), Yii::$app->formatter->asTimestamp($model_date->date_out)]);
        $searchModel = new SchoolplanViewSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);
        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel', 'model_date'));
    }

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                if ($model->setActivitiesOver()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                    $this->getSubmitAction($model);
                }
            }
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        $model->initActivitiesOver();
        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                if ($model->setActivitiesOver($model->activities_over_id)) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                    $this->getSubmitAction($model);
                }
            }
        }
        if (Yii::$app->request->post('submitAction') == 'send_admin_message') {
            if ($model->sendAdminMessage($_POST['Schoolplan'])) {
           // print_r($_POST['Schoolplan']);
                Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                $this->getSubmitAction($model);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionTeachersEfficiency($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'School Plans'), 'url' => ['schoolplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['schoolplan/default/view', 'id' => $id]];

        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {

            $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');

            $modelEfficiency = new TeachersEfficiency();
            $flag = false;
            if ($modelEfficiency->load(Yii::$app->request->post())) {
                $valid = $modelEfficiency->validate();
                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        foreach ($modelEfficiency->teachers_id as $item => $teachers_id) {
                            $m = new TeachersEfficiency();
                            $m->teachers_id = $teachers_id;
                            $m->efficiency_id = $modelEfficiency->efficiency_id;
                            $m->bonus = $modelEfficiency->bonus;
                            $m->date_in = $modelEfficiency->date_in;
                            $m->class = \yii\helpers\StringHelper::basename(get_class($model));
                            $m->item_id = $id;
                            if (!($flag = $m->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                            $this->getSubmitAction($modelEfficiency);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/efficiency/default/_form.php', [
                'model' => $modelEfficiency,
                'class' => StringHelper::basename($this->modelClass::className()),
                'readonly' => false
            ]);
        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['schoolplan/default/teachers-efficiency', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = TeachersEfficiency::findOne($objectId);
            $data = new EfficiencyHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $modelEfficiency = TeachersEfficiency::findOne($objectId);
            $modelEfficiency->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelEfficiency));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['schoolplan/default/teachers-efficiency', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelEfficiency = TeachersEfficiency::findOne($objectId);

            if ($modelEfficiency->load(Yii::$app->request->post()) AND $modelEfficiency->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($modelEfficiency);
            }

            return $this->renderIsAjax('@backend/views/efficiency/default/_form.php', [
                'model' => $modelEfficiency,
                'class' => StringHelper::basename($this->modelClass::className()),
                'readonly' => $readonly
            ]);

        } else {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies')];
            $searchModel = new TeachersEfficiencySearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['class'] = \yii\helpers\StringHelper::basename(get_class($model));
            $params[$searchName]['item_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('teachers-efficiency', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    public function actionProtocolEvent($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'School Plans'), 'url' => ['schoolplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['schoolplan/default/view', 'id' => $id]];

        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');
            $modelProtocol = new SchoolplanProtocol();
            $modelProtocol->schoolplan_id = $id;

            if ($modelProtocol->load(Yii::$app->request->post()) && $modelProtocol->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelProtocol);
            }

            return $this->renderIsAjax('@backend/views/schoolplan/schoolplan-protocol/_form.php', [
                'model' => $modelProtocol,
                'modelsProtocolItems' => (empty($modelsProtocolItems)) ? [new SchoolplanProtocolItems()] : $modelsProtocolItems,
                'readonly' => false
            ]);
        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocols'), 'url' => ['schoolplan/default/protocol-event', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['schoolplan/default/protocol-event', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = SchoolplanProtocol::findOne($objectId);
            $data = new SchoolplanProtocolHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $modelProtocol = SchoolplanProtocol::findOne($objectId);
            $modelProtocol->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelProtocol));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocols'), 'url' => ['schoolplan/default/protocol-event', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelProtocol = SchoolplanProtocol::findOne($objectId);
            $modelsProtocolItems = $modelProtocol->schoolplanProtocolItems;

            if ($modelProtocol->load(Yii::$app->request->post())) {

                $oldIDs = ArrayHelper::map($modelsProtocolItems, 'id', 'id');
                $modelsProtocolItems = Model::createMultiple(SchoolplanProtocolItems::class, $modelsProtocolItems);
                Model::loadMultiple($modelsProtocolItems, Yii::$app->request->post());
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsProtocolItems, 'id', 'id')));
                // validate all models
                $valid = $modelProtocol->validate();
                $valid = Model::validateMultiple($modelsProtocolItems) && $valid;

                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $modelProtocol->save(false)) {
                            if (!empty($deletedIDs)) {
                                SchoolplanProtocolItems::deleteAll(['id' => $deletedIDs]);
                            }
                            foreach ($modelsProtocolItems as $index => $modelProtocolItems) {
                                $modelProtocolItems->schoolplan_protocol_id = $modelProtocol->id;
                                if (!($flag = $modelProtocolItems->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            $this->getSubmitAction();
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/schoolplan/schoolplan-protocol/_form.php', [
                'model' => $modelProtocol,
                'modelsProtocolItems' => (empty($modelsProtocolItems)) ? [new SchoolplanProtocolItems()] : $modelsProtocolItems,
                'readonly' => $readonly
            ]);

        } else {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocols')];
            $searchModel = new SchoolplanProtocolSearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['class'] = \yii\helpers\StringHelper::basename(get_class($model));
            $params[$searchName]['schoolplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('schoolplan-protocol', compact('dataProvider', 'searchModel', 'id'));
        }
    }

    /**
     * @param $id
     * @return array
     * @throws \yii\web\NotFoundHttpException
     */
    public function getMenu($id)
    {
        $model = $this->findModel($id);
        return [
            ['label' => 'Карточка мероприятия', 'url' => ['/schoolplan/default/update', 'id' => $id]],
            ['label' => 'Протоколы мероприятия', 'url' => ['/schoolplan/default/protocol-event', 'id' => $id], 'visible' => !($model->category->commission_sell == 1 && $model->category->commission_sell == 2)],
            ['label' => 'Протоколы аттестационной комиссии', 'url' => ['/schoolplan/default/protocol-attestations', 'id' => $id], 'visible' => $model->category->commission_sell == 1],
            ['label' => 'Протоколы приемной комиссии', 'url' => ['/schoolplan/default/protocol-reception', 'id' => $id], 'visible' => $model->category->commission_sell == 2],
            ['label' => 'Показатели эффективности', 'url' => ['/schoolplan/default/teachers-efficiency', 'id' => $id], 'visible' => $model->category->efficiency_flag],
        ];
    }

    /**
     * @return mixed
     */
    public function actionSelect()
    {
        $id = \Yii::$app->request->post('id');
        $model = GuidePlanTree::find()->where(['id' => $id])->asArray()->one();

        return $model['category_sell'];
    }
}