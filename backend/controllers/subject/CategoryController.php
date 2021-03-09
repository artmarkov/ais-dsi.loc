<?php

namespace backend\controllers\subject;

use himiklab\sortablegrid\SortableGridAction;

/**
 * CategoryItemController implements the CRUD actions for common\models\subject\SubjectCategoryItem model.
 */
class CategoryController extends MainController
{
    public $modelClass       = 'common\models\subject\SubjectCategoryItem';
    public $modelSearchClass = 'common\models\subject\search\SubjectCategoryItemSearch';

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