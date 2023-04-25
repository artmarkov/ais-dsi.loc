<?php

namespace backend\controllers\test;

use common\models\auditory\AuditoryBuilding;
use common\models\auditory\AuditoryCat;
use Yii;
use backend\models\Customer;
use backend\models\CustomerSearch;
use backend\models\Address;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Model;
use yii\web\Response;
use artsoft\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class WizardController extends \backend\controllers\DefaultController
{

    public function actionIndex()
    {
        $shootModel = new AuditoryBuilding();
        $shootTagModel = new AuditoryCat();
        if ($shootModel->load(Yii::$app->request->post()) && $shootTagModel->load(Yii::$app->request->post())) {
            echo '<pre>' . print_r(Yii::$app->request->post(), true) . '</pre>';
        }

        return $this->render('index', [
            'shootsModel' => $shootModel,
            'shootTagModel' => $shootTagModel,
        ]);
    }

}