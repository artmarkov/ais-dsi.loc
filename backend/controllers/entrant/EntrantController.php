<?php

namespace backend\controllers\entrant;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * DefaultController implements the CRUD actions for common\models\entrant\Entrant model.
 */
class EntrantController extends MainController
{
    public $modelClass       = 'common\models\entrant\Entrant';
    public $modelSearchClass = 'common\models\entrant\search\EntrantSearch';

}