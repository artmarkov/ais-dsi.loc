<?php

namespace backend\controllers\subject;

use himiklab\sortablegrid\SortableGridAction;

/**
 * DefaultController implements the CRUD actions for common\models\subject\Subject model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\subject\Subject';
    public $modelSearchClass = 'common\models\subject\search\SubjectSearch';

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