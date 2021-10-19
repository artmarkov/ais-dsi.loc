<?php

namespace frontend\controllers\info;

use frontend\controllers\DefaultController;
use Yii;

/**
 * BoardController implements the CRUD actions for common\models\info\Board model.
 */
class BoardController extends DefaultController
{
    public $modelClass = 'common\models\info\Board';
    public $modelSearchClass = 'common\models\info\search\BoardSearch';

    public function init()
    {
        $this->viewPath = '@backend/views/info/board';

        parent::init();
    }
}