<?php

namespace artsoft\controllers\rest;

use artsoft\behaviors\AccessFilter;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

class RestActiveController extends ActiveController
{   
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::className(),
            HttpBearerAuth::className(),
        ];

        $behaviors['access-filter'] = [
            'class' => AccessFilter::className(),            
        ];
        return $behaviors;
    }  
}