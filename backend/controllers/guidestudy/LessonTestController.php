<?php

namespace backend\controllers\guidestudy;

use himiklab\sortablegrid\SortableGridAction;
use Yii;

/**
 * LessonTestController implements the CRUD actions for common\models\education\LessonTest model.
 */
class LessonTestController extends MainController
{
    public $modelClass       = 'common\models\education\LessonTest';
    public $modelSearchClass = 'common\models\education\search\LessonTestSearch';

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