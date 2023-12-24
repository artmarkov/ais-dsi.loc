<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use common\models\efficiency\search\TeachersEfficiencySearch;
use common\models\efficiency\TeachersEfficiency;
use common\models\teachers\Teachers;
use Yii;

/**
 * EfficiencyController
 */
class EfficiencyController extends MainController
{
    public function actionIndex()
    {
        $this->view->params['breadcrumbs'][] = Yii::t('art/guide', 'Efficiencies');
        $modelTeachers = Teachers::findOne($this->teachers_id);

        $model_date = $this->modelDate;

        $data = ArtHelper::getStudyYearParams($model_date->plan_year);
        $query = TeachersEfficiency::find()->where(['=', 'teachers_id', $this->teachers_id])
            ->andWhere(['and', ['>=', 'date_in', $data['timestamp_in']], ['<=', 'date_in', $data['timestamp_out']]]);
        $searchModel = new TeachersEfficiencySearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('efficiency', compact(['dataProvider', 'searchModel', 'modelTeachers', 'model_date']));

    }

    public function actionView($id)
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['teachers/default/efficiency', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $id);
        $model = TeachersEfficiency::findOne($id);

        return $this->renderIsAjax('@backend/views/efficiency/default/_form.php', [
            'model' => $model,
            'modelDependence' => $modelTeachers,
            'readonly' => true
        ]);
    }

}