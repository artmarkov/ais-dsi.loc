<?php

namespace backend\controllers\guidestudy;

use himiklab\sortablegrid\SortableGridAction;


/**
 * LessonMarkController implements the CRUD actions for common\models\education\LessonMark model.
 */
class LessonMarkController extends MainController
{
    public $modelClass       = 'common\models\education\LessonMark';
    public $modelSearchClass = 'common\models\education\search\LessonMarkSearch';

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