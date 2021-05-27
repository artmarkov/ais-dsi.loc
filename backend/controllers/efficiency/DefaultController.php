<?php

namespace backend\controllers\efficiency;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\ExcelObjectList;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use common\models\efficiency\EfficiencyTree;
use common\models\efficiency\TeachersEfficiency;
use common\models\history\EfficiencyHistory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

    public
    function actionSelect()
    {
        $id = \Yii::$app->request->post('id');
        $model = EfficiencyTree::findOne(['id' => $id]);

        return $model->value_default;
    }

    public
    function actionHistory($id)
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
    public
    function actionSummary()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $day_in = 21;
        $day_out = 20;

        $model_date = new DynamicModel(['date_in', 'date_out']);
        $model_date->addRule(['date_in', 'date_out'], 'required')->addRule(['date_in', 'date_out'], 'date');

        $d = date('d');
        $m = date('m');
        $y = date('Y');

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = $d > $day_in ? ($m + 1 > 12 ? $m - 11 : $m + 1) : $m;
            $year = $mon > 12 ? $y + 1 : $y;

            $model_date->date_in = Yii::$app->formatter->asDate(mktime(0, 0, 0, ($mon - 1), $day_in, $year), 'php:d.m.Y');
            $model_date->date_out = Yii::$app->formatter->asDate(mktime(23, 59, 59, $mon, $day_out, $year), 'php:d.m.Y');
        }

        $root = EfficiencyTree::find()->roots()->select(['name', 'id'])->indexBy('id')->column();
        $data = TeachersEfficiency::getSummaryData($model_date);

        return $this->renderIsAjax('summary', compact(['data', 'root', 'model_date']));
    }

    public
    function actionDetails($id, $date_in, $date_out)
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

    public function actionExcel()
    {
        ini_set('memory_limit', '512M');
//        $dp = $this->getList($searchCondition, false);
//        $list = $dp->getModels();
//        if ($dp->getTotalCount() > self::MAX_EXPORTED_ROWS) {
//            \fdoc\ui\Notice::registerWarning('Превышено ограничение в ' . self::MAX_EXPORTED_ROWS . ' элементов на длину списка, отмена экспорта');
//            return true;
//        }

        try {
//            $columnList = $this->getColumnList();
//            $columns = array_reduce($this->columns, function ($result, $item) use ($columnList) {
//                if ('command' !== $item) {
//                    $result[$item] = $columnList[$item]['name'];
//                }
//                return $result;
//            }, []);
//            $x = new ExcelObjectList($columns);
//            foreach ($list as $id) { // данные
//                $x->addData($this->getData($id, false)['data']);
//            }

            \Yii::$app->response
                ->sendContentAsFile('',  'list.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
                ->send();
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception | \yii\web\RangeNotSatisfiableHttpException $e) {
            \Yii::error('Ошибка формирования xlsx: ' . $e->getMessage());
            \Yii::error($e);
            Yii::$app->session->setFlash('error', 'Ошибка формирования xlsx-выгрузки');
            return true;
        }
    }
}
