<?php

namespace frontend\controllers\teachers;

use common\models\teachers\search\TeachersQualificationsSearch;
use common\models\teachers\TeachersQualifications;
use common\models\teachers\Teachers;
use Yii;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

/**
 * QualificationsController
 */
class QualificationsController extends MainController
{
    public function actionIndex()
    {
        $this->view->params['breadcrumbs'][] = Yii::t('art/guide', 'Qualifications');
        $modelTeachers = Teachers::findOne($this->teachers_id);

        $model_date = $this->modelDate;
        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }
        $searchModel = new TeachersQualificationsSearch();
        $searchName = StringHelper::basename($searchModel::className());
        $params = Yii::$app->request->getQueryParams();
        $params[$searchName]['teachers_id'] = $this->teachers_id;
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('teachers-qualifications', compact(['dataProvider', 'searchModel', 'modelTeachers', 'model_date']));

    }

    public function actionView($id)
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Qualifications'), 'url' => ['teachers/default/teachers', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $id);
        $model = TeachersQualifications::findOne($id);

        return $this->renderIsAjax('@backend/views/qualifications/default/_form.php', [
            'model' => $model,
            'modelTeachers' => $modelTeachers,
            'readonly' => true
        ]);
    }

    public function actionUpdate($id)
    {
        $modelTeachers = Teachers::findOne($this->teachers_id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Qualifications'), 'url' => ['teachers/default/teachers', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $id);
        $model = TeachersQualifications::findOne($id);

        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }
        return $this->renderIsAjax('@backend/views/qualifications/default/_form.php', [
            'model' => $model,
            'modelTeachers' => $modelTeachers,
            'readonly' => false
        ]);
    }

    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Qualifications'), 'url' => ['teachers/qualifications']];
        $this->view->params['breadcrumbs'][] = 'Добавление записи';
        /* @var $model \artsoft\db\ActiveRecord */
        $model = new TeachersQualifications();
        $model->teachers_id = $this->teachers_id;
        $modelTeachers = Teachers::findOne($this->teachers_id);

        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }
        return $this->renderIsAjax('@backend/views/qualifications/default/_form.php', [
            'model' => $model,
            'modelTeachers' => $modelTeachers,
            'readonly' => false
        ]);
    }

    public function actionDelete($id)
    {
        $model = TeachersQualifications::findOne($id);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));
    }

}