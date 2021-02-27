<?php

namespace backend\controllers\venue;

use backend\controllers\DefaultController;
use Yii;

/**
 * DistrictController implements the CRUD actions for common\models\venue\VenueDistrict model.
 */
class DistrictController extends DefaultController
{
    public $modelClass       = 'common\models\venue\VenueDistrict';
    public $modelSearchClass = 'common\models\venue\search\VenueDistrictSearch';

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