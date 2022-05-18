<?php

namespace backend\controllers\question;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * DefaultController implements the CRUD actions for common\models\question\Question model.
 */
class DefaultController extends BaseController 
{
    public $modelClass       = 'common\models\question\Question';
    public $modelSearchClass = 'common\models\question\search\QuestionSearch';

}