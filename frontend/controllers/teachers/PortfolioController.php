<?php

namespace frontend\controllers\teachers;

use artsoft\helpers\ArtHelper;
use common\models\teachers\PortfolioView;
use yii\data\ActiveDataProvider;

/**
 * PortfolioController
 */
class PortfolioController extends MainController
{
    public $modelClass = 'common\models\studyplan\StudyplanThematic';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanThematicSearch';

    public function actionIndex()
    {

        $model_date = $this->modelDate;
        $id = $this->teachers_id;
        $data = ArtHelper::getStudyYearParams($model_date->plan_year);
        $dataProvider = new ActiveDataProvider(['query' => PortfolioView::find()->where(['teachers_id' => $id])->andWhere(['between', 'datetime_in', $data['timestamp_in'], $data['timestamp_out']])]);
        return $this->renderIsAjax('portfolio', compact(['dataProvider', 'model_date', 'id']));

    }

}