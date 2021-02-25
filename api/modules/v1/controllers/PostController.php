<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\PostSearch;
use api\modules\v1\models\Post;
use Yii;
use yii\helpers\Url;
use artsoft\controllers\rest\RestActiveController;
use yii\web\ServerErrorHttpException;

class PostController extends RestActiveController
{
    public $modelClass = 'api\modules\v1\models\Post';

//    public function behaviors()
//    {
//        $behaviors = parent::behaviors();
//        $behaviors['authenticator']['only'] = ['index', 'view', 'create', 'update', 'delete'];        
//        return $behaviors;
//    }
//
//    public function actions()
//    {
//        $actions = parent::actions();
//        unset($actions['create']);
//        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
//        return $actions;
//    }
//
//    public function actionCreate()
//    {
//        $model = new Post();
//        $model->created_by = Yii::$app->user->id;
//
//        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
//        if ($model->save()) {
//            $response = Yii::$app->getResponse();
//            $response->setStatusCode(201);
//            $id = implode(',', array_values($model->getPrimaryKey(true)));
//            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
//        } elseif (!$model->hasErrors()) {
//            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
//        }
//
//        return $model;
//    }
//
//    public function prepareDataProvider()
//    {
//        $searchModel = new PostSearch();
//        return $searchModel->search(Yii::$app->request->queryParams);
//    }
}