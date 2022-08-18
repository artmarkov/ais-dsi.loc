<?php

namespace backend\controllers\guidestudy;

use Yii;

/**
 * GuideTestController implements the CRUD actions for common\models\entrant\GuideEntrantTest model.
 */
class EntrantTestController extends MainController
{
    public $modelClass       = 'common\models\entrant\GuideEntrantTest';
    public $modelSearchClass = 'common\models\entrant\search\GuideEntrantTestSearch';

}