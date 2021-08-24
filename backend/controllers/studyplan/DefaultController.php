<?php

namespace backend\controllers\studyplan;

use common\models\history\StudyplanHistory;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\studyplan\Studyplan model.
 */
class DefaultController extends MainController 
{
    public $modelClass       = 'common\models\studyplan\Studyplan';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanSearch';

    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new StudyplanHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }
}