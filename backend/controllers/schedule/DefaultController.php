<?php

namespace backend\controllers\schedule;



use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\views\SubjectSectScheduleView';
    public $modelSearchClass = '';

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $modelClass = $this->modelClass;
        $searchModel = $this->modelSearchClass ? new $this->modelSearchClass : null;
        $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
            && !User::hasPermission($modelClass::getFullAccessPermission()));

        if ($searchModel) {
            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();

            if ($restrictAccess) {
                $params[$searchName][$modelClass::getOwnerField()] = Yii::$app->user->identity->id;
            }

            $dataProvider = $searchModel->search($params);
        } else {
            $restrictParams = ($restrictAccess) ? [$modelClass::getOwnerField() => Yii::$app->user->identity->id] : [];
            $dataProvider = new ActiveDataProvider(['query' => $modelClass::find()->where($restrictParams)]);
        }

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }
}