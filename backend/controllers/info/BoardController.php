<?php

namespace backend\controllers\info;

use backend\controllers\DefaultController;

/**
 * BoardController implements the CRUD actions for common\models\info\Board model.
 */
class BoardController extends DefaultController
{
    public $modelClass       = 'common\models\info\Board';
    public $modelSearchClass = 'common\models\info\search\BoardSearch';
    public $modelHistoryClass = 'common\models\history\BoardHistory';
}