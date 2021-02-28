<?php

namespace backend\controllers\auditory;

/**
 * AuditoryCatController implements the CRUD actions for common\models\AuditoryCat model.
 */
class CatController extends MainController
{
    public $modelClass       = 'common\models\auditory\AuditoryCat';
    public $modelSearchClass = 'common\models\auditory\search\AuditoryCatSearch';

}