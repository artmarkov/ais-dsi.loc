<?php

namespace frontend\controllers\help;

use frontend\models\SupportForm;
use Yii;

/**
 * SupportController implements the CRUD actions for frontend/models/SupportForm model.
 */
class SupportController extends \frontend\controllers\DefaultController
{
    public function actionIndex()
    {        $model = new SupportForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
        //if nothing suitable was found then throw 404 error
        throw new \yii\web\NotFoundHttpException('Page not found.');
    }


    public function actionAbout()
    {
        return $this->render('about');
        throw new \yii\web\NotFoundHttpException('Page not found.');
    }
}