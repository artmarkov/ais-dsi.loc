<?php

namespace backend\controllers\subject;

/**
 * DefaultController implements the CRUD actions for common\models\subject\Subject model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\subject\Subject';
    public $modelSearchClass = 'common\models\subject\search\SubjectSearch';

}