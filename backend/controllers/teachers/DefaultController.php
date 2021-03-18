<?php

namespace backend\controllers\teachers;

use common\models\guidejob\Stake;
use common\models\guidejob\Cost;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersActivity;
use common\models\user\UserCommon;
use common\models\user\User;
use yii\web\NotFoundHttpException;
use Yii;
use backend\models\Model;
use yii\helpers\ArrayHelper;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\Teachers model.
 */
class DefaultController extends MainController
{

    public $modelClass = 'common\models\teachers\Teachers';
    public $modelSearchClass = 'common\models\teachers\search\TeachersSearch';

    /**
     * на 1 сентября текущего года  - в следующем году данные по стажу автоматически обновятся
     * (в базе ничего не меняется)
     * хранится условная временная метка
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;
        $modelUser = new UserCommon();
        $modelsActivity = [new TeachersActivity];
        $model->time_serv_init = Teachers::getTimeServInit();
        $model->time_serv_spec_init = Teachers::getTimeServInit();

        if ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $modelsActivity = Model::createMultiple(TeachersActivity::classname());
            Model::loadMultiple($modelsActivity, Yii::$app->request->post());

            // validate all models
            $valid = $modelUser->validate();
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsActivity) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $modelUser->user_category = User::USER_CATEGORY_TEACHER;
                $modelUser->status = User::STATUS_INACTIVE;

                $model->timestamp_serv = Teachers::getTimestampServ($model->year_serv, $model->time_serv_init);
                $model->timestamp_serv_spec = Teachers::getTimestampServ($model->year_serv_spec, $model->time_serv_spec_init);

                try {
                    if ($flag = $modelUser->save(false)) {
                        $model->user_id = $modelUser->id;

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
        $modelUser = UserCommon::findOne(['id' => $model->user_id, 'user_category' => User::USER_CATEGORY_TEACHER]);

        if (!isset($model, $modelUser)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsActivity = $model->teachersActivity;

            $model->time_serv_init = Teachers::getTimeServInit();
            $model->time_serv_spec_init = Teachers::getTimeServInit();

            $model->year_serv = Teachers::getYearServ($model->timestamp_serv);
            $model->year_serv_spec = Teachers::getYearServ($model->timestamp_serv_spec);

        if ($modelUser->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsActivity, 'id', 'id');
            $modelsActivity = Model::createMultiple(TeachersActivity::classname(), $modelsActivity);
            Model::loadMultiple($modelsActivity, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsActivity, 'id', 'id')));

            // validate all models
            $valid = $modelUser->validate();
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsActivity) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $model->timestamp_serv = Teachers::getTimestampServ($model->year_serv, $model->time_serv_init);
                $model->timestamp_serv_spec = Teachers::getTimestampServ($model->year_serv_spec, $model->time_serv_spec_init);
//                print_r($model);
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

}
