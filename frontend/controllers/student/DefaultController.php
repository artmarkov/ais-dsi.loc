<?php

namespace frontend\controllers\student;

use backend\models\Model;
use common\models\service\UsersCard;
use common\models\students\Student;
use common\models\students\StudentDependence;
use common\models\user\UserCommon;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * DefaultController
 */
class DefaultController extends MainController
{
    public function actionIndex()
    {
        $this->view->params['breadcrumbs'][] = 'Карточка Ученика';
        $model = Student::findOne($this->student_id);

        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_STUDENTS]);
        $userCard = UsersCard::findOne(['user_common_id' => $model->user_common_id]) ?: new UsersCard();
        // $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsDependence = $model->studentDependence;
        foreach ($modelsDependence as $m) {
            $m->scenario = StudentDependence::SCENARIO_STUDENT;
        }
        if ($userCommon->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post()) && $userCard->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsDependence, 'id', 'id');
            $modelsDependence = Model::createMultiple(StudentDependence::class, $modelsDependence);
            Model::loadMultiple($modelsDependence, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDependence, 'id', 'id')));

            // validate all models
            $valid = $userCommon->validate();
            // $valid = $userCard->validate() && $valid;
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsDependence) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $userCommon->save(false)) {
                        $userCard->user_common_id = $userCommon->id;
                        if ($flag && $flag = $userCard->save(false)) {
                            if ($flag = $model->save(false)) {
                                if (!empty($deletedIDs)) {
                                    StudentDependence::deleteAll(['id' => $deletedIDs]);
                                }
                                foreach ($modelsDependence as $modelDependence) {
                                    $modelDependence->student_id = $model->id;
                                    if (!($flag = $modelDependence->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('_form', [
            'userCommon' => $userCommon,
            'userCard' => $userCard,
            'model' => $model,
            'modelsDependence' => (empty($modelsDependence)) ? [new StudentDependence] : $modelsDependence,
            'readonly' => true
        ]);
    }

}