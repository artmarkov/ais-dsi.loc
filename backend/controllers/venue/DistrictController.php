<?php

namespace backend\controllers\venue;

/**
 * DistrictController implements the CRUD actions for common\models\venue\VenueDistrict model.
 */
class DistrictController extends MainController
{
    public $modelClass       = 'common\models\venue\VenueDistrict';
    public $modelSearchClass = 'common\models\venue\search\VenueDistrictSearch';
}