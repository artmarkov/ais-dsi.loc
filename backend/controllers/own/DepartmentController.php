<?php

namespace backend\controllers\own;

/**
 * DepartmentController implements the CRUD actions for common\models\own\Department model.
 */
class DepartmentController extends MainController
{
    public $modelClass       = 'common\models\own\Department';
    public $modelSearchClass = 'common\models\own\search\DepartmentSearch';
}