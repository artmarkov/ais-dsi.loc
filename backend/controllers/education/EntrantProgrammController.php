<?php

namespace backend\controllers\education;

use Yii;

/**
 * EntrantProgrammController implements the CRUD actions for common\models\entrant\EntrantProgramm model.
 */
class EntrantProgrammController extends MainController
{
    public $modelClass       = 'common\models\education\EntrantProgramm';
    public $modelSearchClass = 'common\models\education\search\EntrantProgrammSearch';

}