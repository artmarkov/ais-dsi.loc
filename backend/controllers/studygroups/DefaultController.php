<?php
/**
 * Created by PhpStorm.
 * User: Zver
 * Date: 05.10.2018
 * Time: 12:14
 */

namespace backend\controllers\studygroups;

use common\models\studygroups\SubjectSect;

class DefaultController  extends MainController
{
    public $modelClass = 'common\models\studygroups\SubjectSect';
    public $modelSearchClass = 'common\models\studygroups\search\SubjectSectSearch';

}