<?php

namespace backend\controllers\test;

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
class QrController extends \backend\controllers\DefaultController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

}