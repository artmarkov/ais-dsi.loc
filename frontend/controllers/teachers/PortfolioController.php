<?php

namespace frontend\controllers\teachers;

use common\models\teachers\PortfolioTeachers;

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
        $data = new PortfolioTeachers($model_date, $id);
        return $this->renderIsAjax('portfolio', [
            'model_date' => $model_date,
            'model' => $data->getData(),
            'id' => $id
        ]);
    }

}