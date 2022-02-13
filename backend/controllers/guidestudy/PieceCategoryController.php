<?php

namespace backend\controllers\guidestudy;

use himiklab\sortablegrid\SortableGridAction;
use Yii;

/**
 * PieceCategoryController implements the CRUD actions for common\models\education\PieceCategory model.
 */
class PieceCategoryController extends MainController
{
    public $modelClass       = 'common\models\education\PieceCategory';
    public $modelSearchClass = 'common\models\education\search\PieceCategorySearch';

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