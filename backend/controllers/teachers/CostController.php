<?php

namespace backend\controllers\teachers;

/**
 * CostController implements the CRUD actions for common\models\teachers\Cost model.
 */
class CostController extends MainController
{
    public $modelClass       = 'common\models\teachers\Cost';
    public $modelSearchClass = 'common\models\teachers\search\CostSearch';
}