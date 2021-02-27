<?php

namespace backend\controllers;

use Yii;


/**
 * MeasureController implements the CRUD actions for backend\models\Measure model.
 */
class MeasureController extends DefaultController
{
    public $modelClass       = 'common\models\Measure';
    public $modelSearchClass = 'common\models\MeasureSearch';
/*
    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            case 'create':
                return ['update', 'id' => $model->id];
                break;
            default:
                return parent::getRedirectPage($action, $model);
        }
    }*/
}
