<?php

namespace backend\controllers\efficiency;

use common\models\efficiency\EfficiencyTree;
use common\models\efficiency\TeachersEfficiency;
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

    public function actionSummary()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $models = $this->modelClass::find()->asArray()->all();
        $root = EfficiencyTree::find()->roots()->select(['name', 'id'])->indexBy('id')->column();
        $tree = EfficiencyTree::find()->leaves()->select(['root', 'id'])->indexBy('id')->column();
        return $this->renderIsAjax('summary', compact(['models', 'root', 'tree']));
    }
}
