<?php

namespace backend\controllers\studyplan;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * LessonProgressController implements the CRUD actions for common\models\education\LessonProgress model.
 */
class LessonProgressController extends BaseController 
{
    public $modelClass       = 'common\models\education\LessonProgress';
    public $modelSearchClass = 'common\models\education\search\LessonProgressSearch';

    public $tabMenu = [
        ['label' => 'Main',  'url' => ['/index']],
    ];
}