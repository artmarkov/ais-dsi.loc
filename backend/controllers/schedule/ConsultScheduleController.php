<?php

namespace backend\controllers\schedule;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * ConsultScheduleController implements the CRUD actions for common\models\schedule\ConsultSchedule model.
 */
class ConsultScheduleController extends BaseController 
{
    public $modelClass       = 'common\models\schedule\ConsultSchedule';
    public $modelSearchClass = 'common\models\schedule\search\ConsultScheduleSearch';

    public $tabMenu = [
        ['label' => 'Main',  'url' => ['/index']],
    ];
}