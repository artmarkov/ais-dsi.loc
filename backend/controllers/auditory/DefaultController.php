<?php

namespace backend\controllers\auditory;

use himiklab\sortablegrid\SortableGridAction;

/**
 * AuditoryController implements the CRUD actions for common\models\Auditory model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\auditory\Auditory';
    public $modelSearchClass = 'common\models\auditory\search\AuditorySearch';
    public $modelHistoryClass = 'common\models\history\AuditoryHistory';

    /**
     * action sort for himiklab\sortablegrid\SortableGridBehavior
     * @return type
     */
    public function actions()
    {
        return [
            'grid-sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => $this->modelClass,
            ],
        ];
    }

}