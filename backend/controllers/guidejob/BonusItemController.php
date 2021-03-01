<?php

namespace backend\controllers\guidejob;

/**
 * BonusItemController implements the CRUD actions for common\models\teachers\BonusItem model.
 */
class BonusItemController extends MainController
{
    public $modelClass       = 'common\models\guidejob\BonusItem';
    public $modelSearchClass = 'common\models\guidejob\search\BonusItemSearch';
}