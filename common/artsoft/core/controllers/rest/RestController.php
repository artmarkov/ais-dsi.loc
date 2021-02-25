<?php

namespace artsoft\controllers\rest;

use artsoft\behaviors\AccessFilter;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class RestController extends Controller
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