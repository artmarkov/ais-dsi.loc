<?php

namespace backend\controllers\question;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use backend\models\Model;
use common\models\question\QuestionAnswers;
use common\models\question\QuestionAttribute;
use common\models\question\QuestionOptions;
use artsoft\controllers\admin\BaseController;
use common\models\question\QuestionUsers;
use common\models\question\QuestionValue;
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
    public $modelSearchClass = 'common\models\question\search\QuestionSearch';
    public $modelHistoryClass = 'common\models\history\QuestionHistory';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $modelsQuestionAttribute = [new QuestionAttribute()];
        $modelsQuestionOptions = [[new QuestionOptions()]];

        if ($model->load(Yii::$app->request->post())) {

            $modelsQuestionAttribute = Model::createMultiple(QuestionAttribute::class);
            Model::loadMultiple($modelsQuestionAttribute, Yii::$app->request->post());

            // validate person and houses models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsQuestionAttribute) && $valid;

            if (isset($_POST['QuestionOptions'])) {
                foreach ($_POST['QuestionOptions'] as $index => $times) {
                    foreach ($times as $indexTime => $time) {
                        $data['QuestionOptions'] = $time;
                        $modelQuestionOptions = new QuestionOptions;
                        $modelQuestionOptions->load($data);
                        $modelsQuestionOptions[$index][$indexTime] = $modelQuestionOptions;
                        $valid = $modelQuestionOptions->validate();
                    }
                }
            }
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsQuestionAttribute as $index => $modelQuestionAttribute) {
                            if ($flag === false) {
                                break;
                            }
                            $modelQuestionAttribute->question_id = $model->id;
                            if (!($flag = $modelQuestionAttribute->save(false))) {
                                break;
                            }
                            if (isset($modelsQuestionOptions[$index]) && is_array($modelsQuestionOptions[$index])) {
                                foreach ($modelsQuestionOptions[$index] as $indexTime => $modelQuestionOptions) {
                                    $modelQuestionOptions->attribute_id = $modelQuestionAttribute->id;
                                    if (!($flag = $modelQuestionOptions->save(false))) {
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax($this->createView, [
                'model' => $model,
                'modelsQuestionAttribute' => (empty($modelsQuestionAttribute)) ? [new QuestionAttribute] : $modelsQuestionAttribute,
                'modelsQuestionOptions' => (empty($modelsQuestionOptions)) ? [[new QuestionOptions]] : $modelsQuestionOptions,
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

        $modelsQuestionAttribute = $model->questionAttributes;
        $modelsQuestionOptions = [];
        $oldTimes = [];

        if (!empty($modelsQuestionAttribute)) {
            foreach ($modelsQuestionAttribute as $index => $modelQuestionAttribute) {
                $times = $modelQuestionAttribute->questionOptions;
                $modelsQuestionOptions[$index] = $times;
                $oldTimes = ArrayHelper::merge(ArrayHelper::index($times, 'id'), $oldTimes);
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            // reset
            $modelsQuestionOptions = [];

            $oldSubjectIDs = ArrayHelper::map($modelsQuestionAttribute, 'id', 'id');
            $modelsQuestionAttribute = Model::createMultiple(QuestionAttribute::class, $modelsQuestionAttribute);
            Model::loadMultiple($modelsQuestionAttribute, Yii::$app->request->post());
            $deletedSubjectIDs = array_diff($oldSubjectIDs, array_filter(ArrayHelper::map($modelsQuestionAttribute, 'id', 'id')));

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsQuestionAttribute) && $valid;
             $valid = true;
            $timesIDs = [];
            if (isset($_POST['QuestionOptions'])) {
                foreach ($_POST['QuestionOptions'] as $index => $times) {
                    $timesIDs = ArrayHelper::merge($timesIDs, array_filter(ArrayHelper::getColumn($times, 'id')));
                    foreach ($times as $indexTime => $time) {
                        $data['QuestionOptions'] = $time;
                        $modelQuestionOptions = (isset($time['id']) && isset($oldTimes[$time['id']])) ? $oldTimes[$time['id']] : new QuestionOptions;
                        $modelQuestionOptions->load($data);
                        $modelsQuestionOptions[$index][$indexTime] = $modelQuestionOptions;
                        $valid = $modelQuestionOptions->validate();
                    }
                }
            }

            $oldTimesIDs = ArrayHelper::getColumn($oldTimes, 'id');
            $deletedTimesIDs = array_diff($oldTimesIDs, $timesIDs);

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {

                        if (!empty($deletedTimesIDs)) {
                            QuestionOptions::deleteAll(['id' => $deletedTimesIDs]);
                        }

                        if (!empty($deletedSubjectIDs)) {
                            QuestionAttribute::deleteAll(['id' => $deletedSubjectIDs]);
                        }

                        foreach ($modelsQuestionAttribute as $index => $modelQuestionAttribute) {

                            if ($flag === false) {
                                break;
                            }
                            $modelQuestionAttribute->question_id = $model->id;
                            if (!($flag = $modelQuestionAttribute->save(false))) {
                                break;
                            }

                            if (isset($modelsQuestionOptions[$index]) && is_array($modelsQuestionOptions[$index])) {
                                foreach ($modelsQuestionOptions[$index] as $indexTime => $modelQuestionOptions) {
                                    $modelQuestionOptions->attribute_id = $modelQuestionAttribute->id;
                                    if (!($flag = $modelQuestionOptions->save(false))) {
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->getSubmitAction($model);
                    }
                } catch (\Exception $e) {
                    echo '<pre>' . print_r($e->getMessage(), true) . '</pre>';
                    $transaction->rollBack();
                }
            }
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
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка формы', 'url' => ['/question/default/update', 'id' => $id]],
            ['label' => 'Ответы', 'url' => ['/question/default/answers', 'id' => $id]],
            // ['label' => 'Статистика', 'url' => ['/question/default/stat', 'id' => $id]],
        ];
    }
}