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
        $student_day_executors_in = 21;
        $student_day_executors_out = 20;

        $model_date = new DynamicModel(['date_in', 'date_out']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'date');

        $root = EfficiencyTree::find()->roots()->select(['name', 'id'])->indexBy('id')->column();

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('d') > $student_day_executors_in ? (date('m') + 1 > 12 ? date('m') - 11 : date('m') + 1) : date('m');
            $year = $mon > 12 ? date('Y') + 1 : date('Y');
            $model_date->date_in = Yii::$app->formatter->asDate($student_day_executors_in . '.' . ($mon - 1) . '.' . $year, 'php:d.m.Y');
            $model_date->date_out = Yii::$app->formatter->asDate( $student_day_executors_out . '.' . $mon . '.' . $year, 'php:d.m.Y');
        }

        $data = TeachersEfficiency::getSummaryData($model_date);

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => array_merge(['id', 'name', 'total', 'stake'], array_keys($root))
            ],
            'pagination' => false,
        ]);
        return $this->renderIsAjax('summary', compact(['dataProvider', 'root', 'model_date']));
    }
}
