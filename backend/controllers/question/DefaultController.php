<?php

namespace backend\controllers\question;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\StringHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use backend\models\Model;
use common\models\history\QuestionAttributeHistory;
use common\models\question\QuestionAnswers;
use common\models\question\QuestionAttribute;
use common\models\question\QuestionOptions;
use artsoft\controllers\admin\BaseController;
use common\models\question\QuestionUsers;
use common\models\question\QuestionValue;
use common\models\question\search\QuestionAttributeSearch;
use himiklab\sortablegrid\SortableGridAction;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\question\Question model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\question\Question';
    public $modelUsersClass = 'common\models\question\QuestionUsers';
    public $modelSearchClass = 'common\models\question\search\QuestionSearch';
    public $modelHistoryClass = 'common\models\history\QuestionHistory';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, [
                'model' => $model,
                'readonly' => false
            ]
        );
    }

    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The Question was not found.");
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->updateView, [
            'model' => $model,
            'modelsQuestionAttribute' => (empty($modelsQuestionAttribute)) ? [new QuestionAttribute] : $modelsQuestionAttribute,
            'modelsQuestionOptions' => (empty($modelsQuestionOptions)) ? [[new QuestionOptions]] : $modelsQuestionOptions,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionAnswers($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $modelVal = new QuestionAnswers(['id' => $id, 'objectId' => $objectId]);

        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['question/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['question/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Answers'), 'url' => ['/question/default/answers', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление ответа';

            if ($modelVal->load(Yii::$app->request->post()) && $modelVal->save()) {
//                echo '<pre>' . print_r($modelVal, true) . '</pre>';
                Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                $this->redirect(['/question/default/answers/', 'id' => $id]);
            }
            return $this->renderIsAjax('/question/answers/_form', [
                'model' => $modelVal,
                'modelQuestion' => $model,
                'readonly' => $readonly,
            ]);


        } elseif ('delete' == $mode && $objectId) {
            $modelVal->delete($objectId);

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Answers'), 'url' => ['/question/default/answers', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $modelVal = $modelVal->getDataOne();
            if (!isset($modelVal)) {
                throw new NotFoundHttpException("The QuestionValue was not found.");
            }
//                echo '<pre>' . print_r($modelVal, true) . '</pre>';
            if ($modelVal->load(Yii::$app->request->post()) && $modelVal->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->redirect(['/question/default/answers/', 'id' => $id]);
            }
            return $this->render('/question/answers/_form', [
                'model' => $modelVal,
                'modelQuestion' => $model,
                'readonly' => $readonly,
            ]);

        } else {
            return $this->renderIsAjax('answers', [
                'data' => $modelVal->getDataArrayAll(),
            ]);
        }
    }

    public function actionQuestionAttribute($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The Question was not found.");
        }

        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['question/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['question/default/view', 'id' => $id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions Attributes'), 'url' => ['/question/default/question-attribute', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = 'Добавление поля формы';

            $modelAttribute = new QuestionAttribute();
            $modelAttribute->question_id = $model->id;
            $modelsItems = [new QuestionOptions()];

            if ($modelAttribute->load(Yii::$app->request->post())) {
                $modelsItems = Model::createMultiple(QuestionOptions::class);
                Model::loadMultiple($modelsItems, Yii::$app->request->post());

                // validate all models
                $valid = $modelAttribute->validate();
                $valid = Model::validateMultiple($modelsItems) && $valid;
                //$valid = true;
                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {

                        if ($flag = $modelAttribute->save(false)) {
                            foreach ($modelsItems as $modelItems) {
                                $modelItems->attribute_id = $modelAttribute->id;
                                if (!($flag = $modelItems->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if ($flag) {
                            $transaction->commit();
                            $this->getSubmitAction($modelAttribute);
                        }
                    } catch (\Exception $e) {
                        print_r($e->getMessage());
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/question/question-attribute/_form.php', [
                'model' => $modelAttribute,
                'modelsQuestionOptions' => (empty($modelsItems)) ? [new QuestionOptions] : $modelsItems,
                'readonly' => $readonly
            ]);

        } elseif ('history' == $mode && $objectId) {
            $modelAttribute = QuestionAttribute::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions Attributes'), 'url' => ['/question/default/question-attribute', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $modelAttribute->id), 'url' => ['/question/default/question-attribute', 'id' => $modelAttribute->id, 'obJectId' => $objectId, 'mode' => 'update']];
            $data = new QuestionAttributeHistory($objectId);
            return $this->renderIsAjax('@backend/views/question/question-attribute/history', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $modelAttribute = QuestionAttribute::findOne($objectId);
            $modelAttribute->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $modelAttribute));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions Attributes'), 'url' => ['/question/default/question-attribute', 'id' => $id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);

            $modelAttribute = QuestionAttribute::findOne($objectId);
            if (!isset($modelAttribute)) {
                throw new NotFoundHttpException("The QuestionAttribute was not found.");
            }
            $modelsItems = $modelAttribute->questionOptions;

            if ($modelAttribute->load(Yii::$app->request->post())) {

                $oldIDs = ArrayHelper::map($modelsItems, 'id', 'id');
                $modelsItems = Model::createMultiple(QuestionOptions::class, $modelsItems);
                Model::loadMultiple($modelsItems, Yii::$app->request->post());
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItems, 'id', 'id')));

                // validate all models
                $valid = $modelAttribute->validate();
//                print_r(Yii::$app->request->post()); die();
                $valid = Model::validateMultiple($modelsItems) && $valid;
               // $valid = true;
                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $modelAttribute->save(false)) {
                            if (!empty($deletedIDs)) {
                                QuestionOptions::deleteAll(['id' => $deletedIDs]);
                            }
                            foreach ($modelsItems as $modelItems) {

                                $modelItems->attribute_id = $modelAttribute->id;
                                if (!($flag = $modelItems->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            $this->getSubmitAction($modelAttribute);
                        }
                    } catch (\Exception $e) {
                        print_r($e->getMessage());
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderIsAjax('@backend/views/question/question-attribute/_form.php', [
                'model' => $modelAttribute,
                'modelsQuestionOptions' => (empty($modelsItems)) ? [new QuestionOptions] : $modelsItems,
                'readonly' => $readonly
            ]);

        } else {
            $searchModel = new QuestionAttributeSearch();

            $searchName = \yii\helpers\StringHelper::basename($searchModel::className());
            $params = $this->getParams();
            $params[$searchName]['question_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('question-attribute', compact('dataProvider', 'searchModel'));
        }
    }

    /**
     * action sort for himiklab\sortablegrid\SortableGridBehavior
     * @return type
     */
    public function actions()
    {
        return [
            'grid-sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => 'common\models\question\QuestionAttribute',
            ],
        ];
    }

    /**
     * Скачивание файла
     * @param $id
     * @return \yii\console\Response|\yii\web\Response
     */
    public function actionDownload($id)
    {
        $content = QuestionValue::findOne(['id' => $id]);
        $tmp_img = Yii::getAlias('@runtime/cache') . DIRECTORY_SEPARATOR . 'attachment.png';
        file_put_contents($tmp_img, base64_decode(stream_get_contents($content->value_file)));
        return Yii::$app->response->sendFile($tmp_img);
    }

    public function actionStat($id)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['question/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['question/default/view', 'id' => $id]];

        $dataProvider = [];

        return $this->renderIsAjax('stat', [
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Activate all selected grid items
     */
    public function actionUsersBulkActivate()
    {
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelUsersClass;
            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $where = ['id' => Yii::$app->request->post('selection', [])];

            if ($restrictAccess) {
                $where[$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $modelClass::updateAll(['read_flag' => 1], $where);
        }
    }

    /**
     * Deactivate all selected grid items
     */
    public function actionUsersBulkDeactivate()
    {
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelUsersClass;
            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));
            $where = ['id' => Yii::$app->request->post('selection', [])];

            if ($restrictAccess) {
                $where[$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $modelClass::updateAll(['read_flag' => 0], $where);
        }
    }

    /**
     * Deactivate all selected grid items
     */
    public function actionUsersBulkDelete()
    {
        if (Yii::$app->request->post('selection')) {
            $modelClass = $this->modelUsersClass;
            $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
                && !User::hasPermission($modelClass::getFullAccessPermission()));

            foreach (Yii::$app->request->post('selection', []) as $id) {
                $where = ['id' => $id];

                if ($restrictAccess) {
                    $where[$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
                }

                $model = $modelClass::findOne($where);

                if ($model) $model->delete();
            }
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка формы', 'url' => ['/question/default/update', 'id' => $id]],
            ['label' => 'Поля формы', 'url' => ['/question/default/question-attribute', 'id' => $id]],
            ['label' => 'Ответы', 'url' => ['/question/default/answers', 'id' => $id]],
            // ['label' => 'Статистика', 'url' => ['/question/default/stat', 'id' => $id]],
        ];
    }
}