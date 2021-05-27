<?php

namespace backend\controllers\efficiency;

use common\models\efficiency\EfficiencyTree;
use common\models\efficiency\TeachersEfficiency;
use common\models\history\EfficiencyHistory;
use Yii;
use yii\base\DynamicModel;


/**
 * DefaultController implements the CRUD actions for common\models\efficiency\Efficiency model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\efficiency\TeachersEfficiency';
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

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSummary()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $day_in = 21;
        $day_out = 20;

        $model_date = new DynamicModel(['date_in', 'date_out']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'date');


        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('d') > $day_in ? (date('m') + 1 > 12 ? date('m') - 11 : date('m') + 1) : date('m');
            $year = $mon > 12 ? date('Y') + 1 : date('Y');
            $model_date->date_in = Yii::$app->formatter->asDate($day_in . '.' . ($mon - 1) . '.' . $year, 'php:d.m.Y');
            $model_date->date_out = Yii::$app->formatter->asDate($day_out . '.' . $mon . '.' . $year, 'php:d.m.Y');
        }

        $root = EfficiencyTree::find()->roots()->select(['name', 'id'])->indexBy('id')->column();
        $data = TeachersEfficiency::getSummaryData($model_date);

        return $this->renderIsAjax('summary', compact(['data', 'root', 'model_date']));
    }

    public function actionDetails($id, $date_in, $date_out)
    {
        $models = $this->modelClass::find()
            ->where(['between', 'date_in', $date_in, $date_out])
            ->andWhere(['=', 'teachers_id', $id])
            ->all();
        echo '<pre>' . print_r($models, true) . '</pre>';
    }
}
