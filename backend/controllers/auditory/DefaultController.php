<?php

namespace backend\controllers\auditory;

use Yii;


/**
 * AuditoryController implements the CRUD actions for common\models\Auditory model.
 */
class DefaultController extends \backend\controllers\DefaultController
{
    public $modelClass       = 'common\models\auditory\Auditory';
    public $modelSearchClass = 'common\models\auditory\search\AuditorySearch';

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