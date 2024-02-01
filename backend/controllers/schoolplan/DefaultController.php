<?php

namespace backend\controllers\schoolplan;

use artsoft\widgets\Notice;
use common\models\efficiency\search\TeachersEfficiencySearch;
use common\models\efficiency\TeachersEfficiency;
use common\models\guidesys\GuidePlanTree;
use common\models\history\EfficiencyHistory;
use common\models\history\SchoolplanProtocolHistory;
use common\models\schoolplan\Schoolplan;
use common\models\schoolplan\SchoolplanPerform;
use common\models\schoolplan\SchoolplanProtocol;
use common\models\schoolplan\SchoolplanProtocolConfirm;
use common\models\schoolplan\SchoolplanView;
use common\models\schoolplan\search\SchoolplanPerformSearch;
use common\models\schoolplan\search\SchoolplanProtocolSearch;
use common\models\studyplan\Studyplan;
use common\models\teachers\TeachersLoadStudyplanView;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\StringHelper;

/**
 * DefaultController implements the CRUD actions for common\models\schoolplan\Schoolplan model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\schoolplan\Schoolplan';
    public $modelSearchClass = 'common\models\schoolplan\search\SchoolplanViewSearch';
    public $modelHistoryClass = 'common\models\history\SchoolplanHistory';

    public $freeAccessActions = ['select'];

    public function actionIndex()
    {
        $session = Yii::$app->session;

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

        $query = SchoolplanView::find()->where(['between', 'datetime_in', Yii::$app->formatter->asTimestamp($model_date->date_in), Yii::$app->formatter->asTimestamp($model_date->date_out) + 86399]);
        $searchModel = new $this->modelSearchClass($query);
        $params = $this->getParams();
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
                if ($model->setActivitiesOver() && $model->save()) {
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
        $model->formPlaces = $model->getFormPlaces();
        $model->title_over = $model->getTitleOver();
        $model->initActivitiesOver();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            print_r($model->errors);
            $model->setActivitiesOver($model->activities_over_id);

            if (Yii::$app->request->post('submitAction') == 'approve') {
                $model->doc_status = Schoolplan::DOC_STATUS_AGREED;
                Yii::$app->session->setFlash('info', Yii::t('art', 'Status successfully changed.'));
                if ($model->approveMessage()) {
                    Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                }
            } elseif (Yii::$app->request->post('submitAction') == 'modif') {
                $model->doc_status = Schoolplan::DOC_STATUS_MODIF;
                Yii::$app->session->setFlash('info', Yii::t('art', 'Status successfully changed.'));
                if ($model->modifMessage()) {
                    Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                }
            }
            if ($model->save(false)) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
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
                            $m->bonus_vid_id = $modelEfficiency->bonus_vid_id;
                            $m->bonus = $modelEfficiency->bonus;
                            $m->date_in = $modelEfficiency->date_in;
                            $m->class = \yii\helpers\StringHelper::basename(get_class($model));
                            $m->item_id = $id;
                            if (!($flag = $m->save(false))) {
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
                'modelDependence' => $model,
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
                'modelDependence' => $model,
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

    public function actionPerform($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $timestamp = Yii::$app->formatter->asTimestamp($model->datetime_in);

        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $timestamp);

        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'School Plans'), 'url' => ['schoolplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['schoolplan/default/view', 'id' => $id]];

        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');
            $modelPerform = new SchoolplanPerform();
            $modelPerform->schoolplan_id = $id;
            $modelPerform->status_sign = 0;

            if ($modelPerform->load(Yii::$app->request->post()) && $modelPerform->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
                $this->getSubmitAction($modelPerform);
            }

            return $this->renderIsAjax('@backend/views/schoolplan/perform/_form.php', [
                'model' => $modelPerform,
                'plan_year' => $plan_year,
                'readonly' => false
            ]);
        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol'), 'url' => ['schoolplan/default/protocol-event', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['schoolplan/default/protocol-event', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
            $model = SchoolplanPerform::findOne($objectId);
            $data = new SchoolplanPerformHistory($objectId);
            return $this->renderIsAjax('@backend/views/history/index.php', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $modelPerform = SchoolplanPerform::findOne($objectId);
            $modelPerform->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelPerform));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Perform'), 'url' => ['schoolplan/default/perform', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelPerform = SchoolplanPerform::findOne($objectId);

            if ($modelPerform->load(Yii::$app->request->post()) && $modelPerform->validate()) {
                if (Yii::$app->request->post('submitAction') == 'approve') {
                    $modelPerform->status_sign = SchoolplanPerform::DOC_STATUS_AGREED;
                    if ($modelPerform->approveMessage()) {
                        Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                    }
                } elseif (Yii::$app->request->post('submitAction') == 'modif') {
                    $modelPerform->status_sign = SchoolplanPerform::DOC_STATUS_MODIF;
                    if ($modelPerform->modifMessage()) {
                        Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                    }
                }
                if ($modelPerform->save()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                    $this->getSubmitAction();
                }
            }
            return $this->renderIsAjax('@backend/views/schoolplan/perform/_form.php', [
                'model' => $modelPerform,
                'plan_year' => $plan_year,
                'readonly' => $readonly
            ]);

        } else {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Perform')];
            $searchModel = new SchoolplanPerformSearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['class'] = \yii\helpers\StringHelper::basename(get_class($model));
            $params[$searchName]['schoolplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('perform', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'modelScoolplan' => $model]);
        }
    }

    public function actionProtocol($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $timestamp = Yii::$app->formatter->asTimestamp($model->datetime_in);

        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $timestamp);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'School Plans'), 'url' => ['schoolplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['schoolplan/default/view', 'id' => $id]];

        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');
            $modelProtocol = new SchoolplanProtocol();
            $modelProtocol->schoolplan_id = $id;
            $flag = true;
            if ($modelProtocol->load(Yii::$app->request->post())) {
                $valid = $modelProtocol->validate();
                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        foreach ($modelProtocol->studyplan_subject_id as $id => $studyplan_subject_id) {
                            $m = new SchoolplanProtocol();
                            $m->setAttributes(
                                [
                                    'thematicFlag' => $modelProtocol->thematicFlag,
                                    'schoolplan_id' => $modelProtocol->schoolplan_id,
                                    'teachers_id' => $modelProtocol->teachers_id,
                                    'thematic_items_list' => $modelProtocol->thematic_items_list,
                                    'studyplan_subject_id' => $studyplan_subject_id,
                                ]
                            );
                            if (!($flag = $m->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                            $this->redirect($this->getRedirectPage('index'));
                        }
                    } catch (\Exception $e) {
                        print_r($e->getMessage());
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/schoolplan/protocol/_form.php', [
                'modelSchoolplan' => $model,
                'model' => $modelProtocol,
                'readonly' => false
            ]);
        } elseif ('history' == $mode && $objectId) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol'), 'url' => ['schoolplan/default/protocol', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $objectId), 'url' => ['schoolplan/default/protocol', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']];
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
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol'), 'url' => ['schoolplan/default/protocol', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelProtocol = SchoolplanProtocol::findOne($objectId);

            if ($modelProtocol->load(Yii::$app->request->post()) && $modelProtocol->validate()) {
                if ($modelProtocol->save()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                    $this->getSubmitAction();
                }
            }
            return $this->renderIsAjax('@backend/views/schoolplan/protocol/_form.php', [
                'modelSchoolplan' => $model,
                'model' => $modelProtocol,
                'readonly' => $readonly
            ]);

        } else {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Protocol')];
            $searchModel = new SchoolplanProtocolSearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['class'] = \yii\helpers\StringHelper::basename(get_class($model));
            $params[$searchName]['schoolplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            $model_confirm = SchoolplanProtocolConfirm::find()->where(['=', 'schoolplan_id', $id])->one() ?? new SchoolplanProtocolConfirm();
            $model_confirm->schoolplan_id = $id;

            if ($model_confirm->load(Yii::$app->request->post()) && $model_confirm->validate()) {
                if (Yii::$app->request->post('submitAction') == 'approve') {
                    $model_confirm->confirm_status = SchoolplanProtocolConfirm::DOC_STATUS_AGREED;
                    if ($model_confirm->approveMessage()) {
                        Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                    }
                } elseif (Yii::$app->request->post('submitAction') == 'modif') {
                    $model_confirm->confirm_status = SchoolplanProtocolConfirm::DOC_STATUS_MODIF;
                    if ($model_confirm->modifMessage()) {
                        Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                    }
                } elseif (Yii::$app->request->post('submitAction') == 'doc_protocol') {
                    $model->makeProtocolDocx();
                }
                if ($model_confirm->save()) {
                    Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                    $this->getSubmitAction();
                }
            }
            return $this->renderIsAjax('protocol', compact('dataProvider', 'searchModel', 'id', 'model_confirm'));
        }
    }


    public function actionStudyplan()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if (!empty($parents)) {
                $model = Schoolplan::findOne(['id' => $_GET['id']]);
                $out = $model->getStudyplanSubjectListById($parents[0]);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    public function actionStudyplanThematic()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $model = Schoolplan::findOne($_GET['id']);
                $out = $model->getStudyplanThematicItemsById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
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
            ['label' => 'Карточка мероприятия', 'url' => ['/schoolplan/default/view', 'id' => $id]],
            ['label' => 'Выполнение плана и участие в мероприятии', 'url' => ['/schoolplan/default/perform', 'id' => $id], 'visible' => $model->category->commission_sell == 0],
            ['label' => 'Протокол аттестационной комиссии', 'url' => ['/schoolplan/default/protocol', 'id' => $id], 'visible' => $model->category->commission_sell == 1],
//            ['label' => 'Протоколы приемной комиссии', 'url' => ['/schoolplan/default/protocol-reception', 'id' => $id], 'visible' => $model->category->commission_sell == 2],
            ['label' => 'Показатели эффективности', 'url' => ['/schoolplan/default/teachers-efficiency', 'id' => $id]/*, 'visible' => $model->category->efficiency_flag*/],
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