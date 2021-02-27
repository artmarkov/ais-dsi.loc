<?php

namespace backend\controllers\creative;

use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\creative\CreativeWorks model.
 */
class DefaultController extends \backend\controllers\DefaultController
{
    public $modelClass       = 'common\models\creative\CreativeWorks';
    public $modelSearchClass = 'common\models\creative\search\CreativeWorksSearch';

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