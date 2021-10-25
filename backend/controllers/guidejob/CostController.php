<?php

namespace backend\controllers\guidejob;

/**
 * CostController implements the CRUD actions for common\models\teachers\Cost model.
 */
class CostController extends MainController
{
    public $modelClass       = 'common\models\guidejob\Cost';
    public $modelSearchClass = 'common\models\guidejob\search\CostSearch';
    public $modelHistoryClass = 'common\models\history\CostHistory';

}