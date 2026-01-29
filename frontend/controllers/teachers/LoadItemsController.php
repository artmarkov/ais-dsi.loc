<?php

namespace frontend\controllers\teachers;

use common\models\studyplan\StudyplanSubjectHist;
use common\models\teachers\search\TeachersLoadViewSearch;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadView;
use Yii;

/**
 * LoadItemsController
 */
class LoadItemsController extends MainController
{
    public function actionIndex()
    {
        $model = Teachers::findOne($this->teachers_id);

        $model_date = $this->modelDate;

        $query = TeachersLoadView::find()->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($this->teachers_id)])
            ->andWhere(['=', 'plan_year', $model_date->plan_year])
            ->andWhere(['not in', 'studyplan_subject_id', StudyplanSubjectHist::getStudyplanSubjectPass()]);
        $searchModel = new TeachersLoadViewSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('load-items', compact('dataProvider', 'searchModel', 'model_date', 'model'));

    }

}