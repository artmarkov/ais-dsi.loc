<?php

namespace backend\controllers\education;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * DefaultController implements the CRUD actions for common\models\education\EducationProgramm model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\education\EducationProgramm';
    public $modelSearchClass = 'common\models\education\search\EducationProgrammSearch';

}