<?php

namespace backend\controllers\efficiency;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use common\models\efficiency\EfficiencyTree;
use common\models\efficiency\TeachersEfficiency;
use common\models\history\EfficiencyHistory;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;


/**
 * DefaultController implements the CRUD actions for common\models\efficiency\Efficiency model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\efficiency\TeachersEfficiency';
    public $modelSearchClass = 'common\models\efficiency\search\TeachersEfficiencySearch';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    foreach ($model->teachers_id as $id => $teachers_id) {
                        $m = new $this->modelClass;
                        $m->teachers_id = $teachers_id;
                        $m->efficiency_id = $model->efficiency_id;
                        $m->bonus = $model->bonus;
                        $m->date_in = $model->date_in;
                        if (!($flag = $m->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                        $this->redirect($this->getRedirectPage('index'));
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax($this->createView, compact('model'));
    }

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
    public function actionSummary($view = 'summary')
    {
        $session = Yii::$app->session;

        $this->view->params['tabMenu'] = $this->tabMenu;
        $day_in = Yii::$app->settings->get('module.day_in', 21);
        $day_out = Yii::$app->settings->get('module.day_out', 20);

        $model_date = new DynamicModel(['date_in', 'date_out', 'hidden_flag']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'date')
            ->addRule('hidden_flag', 'integer');

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $d = date('d');
            $m = date('m');
            $y = date('Y');

            $mon = $d > $day_in ? ($m == 12 ? 1 : $m + 1) : $m;
            $year = $m == 12 ? $y + 1 : $y;

            $model_date->date_in = $session->get('_efficiency_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, ($mon - 1), $day_in, $year), 'php:d.m.Y');
            $model_date->date_out = $session->get('_efficiency_date_out') ?? Yii::$app->formatter->asDate(mktime(23, 59, 59, $mon, $day_out, $year), 'php:d.m.Y');
            $model_date->hidden_flag = $session->get('_efficiency_hidden_flag') ?? 0;
        }
        $session->set('_efficiency_date_in', $model_date->date_in);
        $session->set('_efficiency_date_out', $model_date->date_out);
        $session->set('_efficiency_hidden_flag', $model_date->hidden_flag);

        $data = TeachersEfficiency::getSummaryData($model_date);

        if (Yii::$app->request->post('submitAction') == 'excel') {
            TeachersEfficiency::sendXlsx($data);
        }

        return $this->renderIsAjax($view, compact(['data', 'model_date']));
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBar()
    {
       return $this->actionSummary('bar');
    }

    /**
     * @param $id
     * @param $date_in
     * @param $date_out
     * @return string|\yii\web\Response
     */
    public function actionDetails($id, $date_in, $date_out)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => 'Сводная таблица', 'url' => ['/efficiency/default/summary']];

        $modelClass = $this->modelClass;
        $searchModel = null;
        $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
            && !User::hasPermission($modelClass::getFullAccessPermission()));

        $restrictParams = ($restrictAccess) ? [$modelClass::getOwnerField() => Yii::$app->user->identity->id] : [];
        $dataProvider = new ActiveDataProvider(['query' => $modelClass::find()->where($restrictParams)
            ->andWhere(['between', 'date_in', $date_in, $date_out])
            ->andWhere(['=', 'teachers_id', $id]), 'pagination' => false
        ]);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }

}
