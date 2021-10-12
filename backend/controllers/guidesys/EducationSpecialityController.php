<?php

namespace backend\controllers\guidesys;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * EducationSpecialityController implements the CRUD actions for common\models\education\EducationSpeciality model.
 */
class EducationSpecialityController extends MainController
{
    public $modelClass       = 'common\models\education\EducationSpeciality';
    public $modelSearchClass = 'common\models\education\search\EducationSpecialitySearch';

}