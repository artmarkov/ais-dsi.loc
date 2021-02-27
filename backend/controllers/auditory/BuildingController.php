<?php

namespace backend\controllers\auditory;

use backend\controllers\DefaultController;
use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * AuditoryBuildingController implements the CRUD actions for common\models\AuditoryBuilding model.
 */
class BuildingController extends DefaultController
{
    public $modelClass       = 'common\models\auditory\AuditoryBuilding';
    public $modelSearchClass = 'common\models\auditory\search\AuditoryBuildingSearch';

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
    }
}