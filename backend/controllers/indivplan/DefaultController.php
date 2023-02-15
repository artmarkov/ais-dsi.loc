<?php

namespace backend\controllers\indivplan;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\TeachersPlan model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\teachers\TeachersPlan';
    public $modelSearchClass = 'common\models\teachers\search\TeachersPlanSearch';
    public $modelHistoryClass = 'common\models\history\TeachersPlanHistory';


}