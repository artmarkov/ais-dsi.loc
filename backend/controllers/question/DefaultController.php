<?php

namespace backend\controllers\question;

use backend\models\Model;
use common\models\question\QuestionAttribute;
use common\models\question\QuestionOptions;
use artsoft\controllers\admin\BaseController;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\question\Question model.
 */
class DefaultController extends BaseController 
{
    public $modelClass       = 'common\models\question\Question';
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

            if (isset($_POST['QuestionOptions'][0][0])) {
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
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The EducationProgramm was not found.");
        }

        $modelsQuestionAttribute = $model->questionAttributes;
        $modelsQuestionOptions = [];
        $oldTimes = [];

        if (!empty($modelsQuestionAttribute)) {
            foreach ($modelsQuestionAttribute as $index => $modelQuestionAttribute) {
                $times = $modelQuestionAttribute->QuestionOptions;
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

            $timesIDs = [];
            if (isset($_POST['QuestionOptions'][0][0])) {
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
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
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

}