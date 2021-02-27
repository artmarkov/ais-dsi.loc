<?php

namespace backend\controllers\auditory;

use backend\controllers\DefaultController;
use Yii;

/**
 * AuditoryCatController implements the CRUD actions for common\models\AuditoryCat model.
 */
class CatController extends DefaultController
{
    public $modelClass       = 'common\models\auditory\AuditoryCat';
    public $modelSearchClass = 'common\models\auditory\search\AuditoryCatSearch';

    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            case 'create':
                return ['update', 'id' => $model->id];
                break;
            default:
                return parent::getRedirectPage($action, $model);
        }
    }
}