<?php

namespace backend\controllers\venue;

/**
 * SityController implements the CRUD actions for common\models\venue\VenueSity model.
 */
class SityController extends MainController
{
    public $modelClass       = 'common\models\venue\VenueSity';
    public $modelSearchClass = 'common\models\venue\search\VenueSitySearch';
}