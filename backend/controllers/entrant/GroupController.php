<?php

namespace backend\controllers\entrant;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * GroupController implements the CRUD actions for common\models\entrant\EntrantGroup model.
 */
class GroupController extends MainController
{
    public $modelClass       = 'common\models\entrant\EntrantGroup';
    public $modelSearchClass = 'common\models\entrant\search\EntrantGroupSearch';

}