<?php

namespace backend\controllers\schoolplan;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * ActivitiesPlanController implements the CRUD actions for common\models\activities\ActivitiesPlan model.
 */
class DefaultController extends BaseController
{
    public $modelClass       = 'common\models\schoolplan\Schoolplan';
    public $modelSearchClass = 'common\models\schoolplan\search\SchoolplanSearch';

}