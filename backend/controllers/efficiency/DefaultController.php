<?php

namespace backend\controllers\efficiency;

use common\models\efficiency\EfficiencyTree;
use common\models\history\EfficiencyHistory;
use Yii;


/**
 * DefaultController implements the CRUD actions for common\models\efficiency\Efficiency model.
 */
class DefaultController extends MainController
{
    public $modelClass       = 'common\models\efficiency\TeachersEfficiency';
    public $modelSearchClass = 'common\models\efficiency\search\TeachersEfficiencySearch';

    public function actionSelect()
    {
        $id = \Yii::$app->request->post('id');
        $model = EfficiencyTree::findOne(['id' => $id]);

        return $model->value_default;
    }

    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new EfficiencyHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }
}
