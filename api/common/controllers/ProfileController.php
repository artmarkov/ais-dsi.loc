<?php

namespace api\common\controllers;

use Yii;
use api\common\models\User;
use artsoft\controllers\rest\RestController;
use yii\web\ServerErrorHttpException;

class ProfileController extends RestController
{  
    /**
     * @return type
     */
    public function actionIndex()
    {
        return $this->findModel();
    }

    public function actionUpdate()
    {
        $model = $this->findModel();

        $model->load(Yii::$app->request->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    public function verbs()
    {
        return [
            'index' => ['get'],
            'update' => ['put', 'patch'],
        ];
    }

    /**
     * @return User
     */
    private function findModel()
    {
        return User::findOne(Yii::$app->user->id);
    }
}