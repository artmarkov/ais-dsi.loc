<?php

namespace frontend\controllers\teachers;

use yii\helpers\StringHelper;
use common\models\efficiency\TeachersEfficiency;
use yii\web\NotFoundHttpException;

/**
 * CreativeController
 */
class CreativeController extends MainController
{
    public $modelClass = 'common\models\creative\CreativeWorks';
    public $modelSearchClass = 'common\models\creative\search\CreativeWorksSearch';

    public function actionIndex()
    {

        $query = $this->modelClass::find()->where(['like', 'teachers_list', $this->teachers_id]);

        $searchModel = new $this->modelSearchClass($query);
        $params = \Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('@backend/views/creative/default/index', compact('dataProvider', 'searchModel'));

    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     */
    public function actionView($id, $readonly = true)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The CreativeWorks was not found.");
        }

        $modelsEfficiency = $model->teachersEfficiency;

        return $this->render('@backend/views/creative/default/update', [
            'model' => $model,
            'modelsEfficiency' => (empty($modelsEfficiency)) ? [new TeachersEfficiency()] : $modelsEfficiency,
            'class' => StringHelper::basename($this->modelClass::className()),
            'readonly' => $readonly
        ]);
    }
}