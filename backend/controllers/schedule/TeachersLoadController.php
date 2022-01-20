<?php

namespace backend\controllers\schedule;

use artsoft\models\User;
use common\models\guidejob\Bonus;
use common\models\subjectsect\search\SubjectScheduleViewSearch;
use common\models\subjectsect\SubjectSchedule;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subject\Subject;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersActivity;
use common\models\teachers\TeachersLoad;
use common\models\user\UserCommon;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;
use backend\models\Model;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\TeachersLoad model.
 */
class TeachersLoadController extends MainController
{

    public $modelClass = 'common\models\teachers\TeachersLoad';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
            throw new NotFoundHttpException("Отсутствует обязательные параметры GET studyplan_subject_id или subject_sect_studyplan_id.");
        }
        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id') ?? 0;
        $model->subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id') ?? 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax('@backend/views/teachers/teachers-load/create', [
            'model' => $model
        ]);
    }

    public function actionUpdate($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax('@backend/views/teachers/teachers-load/update', [
            'model' => $model
        ]);
    }

    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'delete':
                return ['/schedule'];
                break;
            case 'create':
            case 'update':
                return ['/schedule/teachers-load/update', 'id' => $model->id];
                break;
            default:
                return ['/schedule'];
        }
    }


}
