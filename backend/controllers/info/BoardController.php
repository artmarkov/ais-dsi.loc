<?php

namespace backend\controllers\info;

use backend\controllers\DefaultController;
use Yii;

/**
 * BoardController implements the CRUD actions for common\models\info\Board model.
 */
class BoardController extends DefaultController
{
    public $modelClass       = 'common\models\info\Board';
    public $modelSearchClass = 'common\models\info\search\BoardSearch';

}