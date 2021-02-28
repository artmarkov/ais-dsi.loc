<?php

namespace backend\controllers\creative;

/**
 * DefaultController implements the CRUD actions for common\models\creative\CreativeWorks model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\creative\CreativeWorks';
    public $modelSearchClass = 'common\models\creative\search\CreativeWorksSearch';
}