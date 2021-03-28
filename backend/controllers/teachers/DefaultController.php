<?php

namespace backend\controllers\teachers;

use common\models\guidejob\Bonus;
use common\models\history\TeachersHistory;
use common\models\teachers\TeachersActivity;
use common\models\user\UserCommon;
use yii\web\NotFoundHttpException;
use backend\models\Model;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\Teachers model.
 */
class DefaultController extends MainController
{

    public $modelClass = 'common\models\teachers\Teachers';
    public $modelSearchClass = 'common\models\teachers\search\TeachersSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $modelUser = new UserCommon();
        $model = new $this->modelClass;
        $modelsActivity = [new TeachersActivity];

        if ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $modelsActivity = Model::createMultiple(TeachersActivity::class);
            Model::loadMultiple($modelsActivity, Yii::$app->request->post());

            // validate all models
            $valid = $modelUser->validate();
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsActivity) && $valid;
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $modelUser->user_category = UserCommon::USER_CATEGORY_TEACHER;
                $modelUser->status = UserCommon::STATUS_INACTIVE;
                try {
                    if ($flag = $modelUser->save(false)) {
                        $model->user_common_id = $modelUser->id;
                        if ($flag = $model->save(false)) {
                            foreach ($modelsActivity as $modelActivity) {
                                $modelActivity->teachers_id = $model->id;
                                if (!($flag = $modelActivity->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
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
            'modelUser' => $modelUser,
            'model' => $model,
            'modelsActivity' => (empty($modelsActivity)) ? [new TeachersActivity] : $modelsActivity,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);
        $modelUser = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_TEACHER]);

        if (!isset($model, $modelUser)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsActivity = $model->teachersActivity;

        if ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsActivity, 'id', 'id');
            $modelsActivity = Model::createMultiple(TeachersActivity::class, $modelsActivity);
            Model::loadMultiple($modelsActivity, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsActivity, 'id', 'id')));

            // validate all models
            $valid = $modelUser->validate();
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsActivity) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelUser->save(false)) {
                        if ($flag = $model->save(false)) {
                            if (!empty($deletedIDs)) {
                                TeachersActivity::deleteAll(['id' => $deletedIDs]);
                            }
                            foreach ($modelsActivity as $modelActivity) {
                                $modelActivity->teachers_id = $model->id;
                                if (!($flag = $modelActivity->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
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

        return $this->render('update', [
            'modelUser' => $modelUser,
            'model' => $model,
            'modelsActivity' => (empty($modelsActivity)) ? [new TeachersActivity] : $modelsActivity,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionSelect()
    {
        $id = \Yii::$app->request->post('id');
        $model = Bonus::findOne(['id' => $id]);

        return $model->value_default;
    }

    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new TeachersHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }

}
