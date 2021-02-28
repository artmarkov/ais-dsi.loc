<?php

namespace backend\controllers\auditory;

/**
 * AuditoryController implements the CRUD actions for common\models\Auditory model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\auditory\Auditory';
    public $modelSearchClass = 'common\models\auditory\search\AuditorySearch';

}