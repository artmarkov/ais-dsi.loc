<?php

namespace frontend\controllers;

use frontend\models\ContactForm;
use frontend\components\NumericCaptcha;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Site controller
 */
class SiteController extends DashboardController
{
    public $freeAccess = true;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'error' => [
                'class' => 'artsoft\web\ErrorAction',
            ],
            'captcha' => [
                'class' => NumericCaptcha::className(),
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ]);
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            $this->redirect('dashboard');
        }
        return $this->render('index');
        //if nothing suitable was found then throw 404 error
        throw new \yii\web\NotFoundHttpException('Page not found.');
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', Yii::t('art/mail', 'Thank you for contacting us. We will respond to you as soon as possible.'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('art/mail', 'There was an error sending email.'));
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionCommonRules()
    {
        return $this->render('common-rules');
        //if nothing suitable was found then throw 404 error
        throw new \yii\web\NotFoundHttpException('Page not found.');
    }

    public function actionPrivacyPolicy()
    {
        return $this->render('privacy-policy');
        //if nothing suitable was found then throw 404 error
        throw new \yii\web\NotFoundHttpException('Page not found.');
    }

    public function actionPreregistration()
    {

        return $this->render('preregistration');

    }
}