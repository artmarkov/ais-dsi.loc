<?php

namespace backend\controllers\studyplan;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * LessonItemsController implements the CRUD actions for common\models\education\LessonItems model.
 */
class LessonItemsController extends BaseController 
{
    public $modelClass       = 'common\models\education\LessonItems';
    public $modelSearchClass = 'common\models\education\search\LessonItemsSearch';

    public $tabMenu = [
        ['label' => 'Main',  'url' => ['/index']],
    ];
}