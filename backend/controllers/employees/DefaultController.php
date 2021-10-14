<?php

namespace backend\controllers\employees;

use artsoft\models\User;
use common\models\history\EmployeesHistory;
use common\models\teachers\TeachersActivity;
use common\models\user\UserCommon;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\employees\Employees model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\employees\Employees';
    public $modelSearchClass = 'common\models\employees\search\EmployeesSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $user = new User();
        $userCommon = new UserCommon();
        $userCommon->scenario = UserCommon::SCENARIO_NEW;
        $model = new $this->modelClass;

        if ($userCommon->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {
            // validate all models
            $valid = $userCommon->validate();
            $valid = $model->validate() && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $user->username = $userCommon->generateUsername();
                    $user->email = $userCommon->email;
                    $user->generateAuthKey();

                    if (Yii::$app->art->emailConfirmationRequired) {
                        $user->status = User::STATUS_INACTIVE;
                        $user->generateConfirmationToken();
                    }
                    if ($flag = $user->save(false)) {
                        $user->assignRoles(['user', 'employees']);
                        $userCommon->user_category = UserCommon::USER_CATEGORY_EMPLOYEES;
                        $userCommon->user_id = $user->id;
                        if ($flag = $userCommon->save(false)) {
                            $model->user_common_id = $userCommon->id;
                            $flag = $model->save(false);
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
            'userCommon' => $userCommon,
            'model' => $model,
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
        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_EMPLOYEES]);
        $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }
        if ($userCommon->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {
            // validate all models
            $valid = $userCommon->validate();
            $valid = $model->validate() && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $userCommon->save(false)) {
                        $flag = $model->save(false);
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
            'userCommon' => $userCommon,
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new EmployeesHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }
}