<?php

namespace backend\controllers\venue;

use backend\controllers\DefaultController;
use Yii;

/**
 * CountryController implements the CRUD actions for common\models\venue\VenueCountry model.
 */
class CountryController extends DefaultController
{
    public $modelClass       = 'common\models\venue\VenueCountry';
    public $modelSearchClass = 'common\models\venue\search\VenueCountrySearch';

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