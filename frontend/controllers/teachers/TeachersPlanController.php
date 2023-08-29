<?php

namespace frontend\controllers\teachers;

use common\models\teachers\Teachers;
use Yii;

/**
 * TeachersPlanController implements the CRUD actions for common\models\teachers\TeachersPlan model.
 */
class TeachersPlanController extends MainController
{
    public $modelClass = 'common\models\teachers\TeachersPlan';
    public $modelSearchClass = 'common\models\teachers\search\TeachersPlanSearch';
    public $modelHistoryClass = 'common\models\history\teachersPlanHistory';

    public function actionIndex()
    {
        $model_date = $this->modelDate;
        $modelTeachers = Teachers::findOne($this->teachers_id);

        $query = $this->modelClass::find()->where(['=', 'teachers_id', $modelTeachers->id])->andWhere(['=', 'plan_year', $model_date->plan_year]);

        $searchModel = new $this->modelSearchClass($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('teachers-plan', compact('dataProvider', 'searchModel', 'model_date', 'modelTeachers'));

    }

}