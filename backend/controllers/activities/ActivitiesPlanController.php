<?php

namespace backend\controllers\activities;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * ActivitiesPlanController implements the CRUD actions for common\models\activities\ActivitiesPlan model.
 */
class ActivitiesPlanController extends BaseController 
{
    public $modelClass       = 'common\models\activities\ActivitiesPlan';
    public $modelSearchClass = 'common\models\activities\search\ActivitiesPlanSearch';

    public $tabMenu = [
        ['label' => 'Main',  'url' => ['/index']],
    ];
}