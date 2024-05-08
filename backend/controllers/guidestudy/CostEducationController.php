<?php

namespace backend\controllers\guidestudy;

use common\models\education\CostEducation;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

/**
 * EducationLevelController implements the CRUD actions for common\models\education\CostEducation model.
 */
class CostEducationController extends MainController
{
    public $modelClass = 'common\models\education\CostEducation';
    public $modelSearchClass = '';

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $modelClass = $this->modelClass;
        $searchModel = null;
        CostEducation::initModels();
        $dataProvider = new ActiveDataProvider([
            'query' => $modelClass::find(),
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }

    public function actionSetStandartBasic()
    {
        $id = $_GET['id'];

        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (isset($_POST['standard_basic'])) {
                $model = $this->modelClass::findOne($id);
                $model->standard_basic = $_POST['standard_basic'];
                $model->save(false);
                return Json::encode(['output' => $model->standard_basic, 'message' => '']);
            } else {
                return Json::encode(['output' => '', 'message' => '']);
            }
        }

        return null;
    }

    public function actionSetBasicRatio()
    {
        $id = $_GET['id'];

        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (isset($_POST['standard_basic_ratio'])) {
                $model = $this->modelClass::findOne($id);
                $model->standard_basic_ratio = $_POST['standard_basic_ratio'];
                $model->save(false);
                return Json::encode(['output' => $model->standard_basic_ratio, 'message' => '']);
            } else {
                return Json::encode(['output' => '', 'message' => '']);
            }
        }

        return null;
    }
}