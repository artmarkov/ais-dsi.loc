<?php

namespace backend\controllers\auditory;

use himiklab\sortablegrid\SortableGridAction;
use common\models\history\AuditoryHistory;

/**
 * AuditoryController implements the CRUD actions for common\models\Auditory model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\auditory\Auditory';
    public $modelSearchClass = 'common\models\auditory\search\AuditorySearch';

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

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new AuditoryHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }
}