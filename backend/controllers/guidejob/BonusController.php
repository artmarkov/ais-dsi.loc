<?php

namespace backend\controllers\guidejob;

/**
 * BonusItemController implements the CRUD actions for common\models\teachers\Bonus model.
 */
class BonusController extends MainController
{
    public $modelClass       = 'common\models\guidejob\Bonus';
    public $modelSearchClass = 'common\models\guidejob\search\BonusSearch';
}