<?php

namespace frontend\controllers\teachers;

use common\models\students\Student;
use common\models\studyplan\search\StudyplanSearch;
use common\models\studyplan\Studyplan;
use common\models\teachers\TeachersLoadStudyplanView;
use Yii;

/**
 * StudentsController
 */
class StudentsController extends MainController
{
    public function actionIndex()
    {
        $model_date = $this->modelDate;
        $students = TeachersLoadStudyplanView::find()
            ->select('student_id')
            ->distinct('student_id')
            ->where(['=', 'teachers_id', $this->teachers_id])
            ->column();

        $query = Studyplan::find()->where(['in', 'student_id', $students])
            ->andWhere(['=', 'plan_year', $model_date->plan_year])
            ->andWhere(['=', 'studyplan.status', 1])
            ->orderBy('student_id');

        $searchModel = new StudyplanSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);
        return $this->renderIsAjax('@backend/views/studyplan/default/index.php', compact('dataProvider', 'searchModel', 'model_date'));


    }

    public function actionView($id){
        $model = Studyplan::findOne($id);
        $modelStudent = Student::findOne($model->student_id);
        $studentDependence = $modelStudent->studentDependence;
        $this->view->params['breadcrumbs'][] = ['label' => 'Карточка ученика'];
        return $this->renderIsAjax('@backend/views/studyplan/default/students_view', compact('modelStudent', 'studentDependence'));
    }

}