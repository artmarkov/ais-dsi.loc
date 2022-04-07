<?php

namespace backend\controllers\info;

use Yii;
use artsoft\controllers\admin\BaseController;

/**
 * DocumentController implements the CRUD actions for common\models\info\Document model.
 */
class DocumentController extends BaseController 
{
    public $modelClass       = 'common\models\info\Document';
    public $modelSearchClass = 'common\models\info\search\DocumentSearch';

    public $tabMenu = [
        ['label' => 'Main',  'url' => ['/index']],
    ];
}