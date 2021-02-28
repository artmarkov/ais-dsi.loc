<?php

namespace backend\controllers\venue;

/**
 * CountryController implements the CRUD actions for common\models\venue\VenueCountry model.
 */
class CountryController extends MainController
{
    public $modelClass       = 'common\models\venue\VenueCountry';
    public $modelSearchClass = 'common\models\venue\search\VenueCountrySearch';
}