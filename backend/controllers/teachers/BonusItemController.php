<?php

namespace backend\controllers\teachers;

/**
 * BonusItemController implements the CRUD actions for common\models\teachers\BonusItem model.
 */
class BonusItemController extends MainController
{
    public $modelClass       = 'common\models\teachers\BonusItem';
    public $modelSearchClass = 'common\models\teachers\search\BonusItemSearch';
}