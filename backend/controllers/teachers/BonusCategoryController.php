<?php

namespace backend\controllers\teachers;


/**
 * BonusCategoryController implements the CRUD actions for common\models\teachers\BonusCategory model.
 */
class BonusCategoryController extends MainController
{
    public $modelClass       = 'common\models\teachers\BonusCategory';
    public $modelSearchClass = 'common\models\teachers\search\BonusCategorySearch';
}