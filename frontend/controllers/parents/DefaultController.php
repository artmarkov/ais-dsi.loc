<?php

namespace frontend\controllers\parents;

use common\models\parents\Parents;
use common\models\service\UsersCard;
use common\models\students\StudentDependence;
use common\models\user\UserCommon;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * DefaultController
 */
class DefaultController extends MainController
{
    public function actionIndex()
    {
        $this->view->params['breadcrumbs'][] = 'Карточка родителя';
        $model = Parents::findOne($this->parents_id);

        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_PARENTS]);
        $userCard = UsersCard::findOne(['user_common_id' => $model->user_common_id]) ?: new UsersCard();
        // $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsDependence = $model->studentDependence;
        foreach ($modelsDependence as $m) {
            $m->scenario = StudentDependence::SCENARIO_STUDENT;
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