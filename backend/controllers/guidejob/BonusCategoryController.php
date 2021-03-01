<?php

namespace backend\controllers\guidejob;


/**
 * BonusCategoryController implements the CRUD actions for common\models\teachers\BonusCategory model.
 */
class BonusCategoryController extends MainController
{
    public $modelClass       = 'common\models\guidejob\BonusCategory';
    public $modelSearchClass = 'common\models\guidejob\search\BonusCategorySearch';
}