<?php

namespace backend\controllers\invoices;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use common\models\education\EducationProgramm;
use common\models\studyplan\search\StudyplanInvoicesViewSearch;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanInvoices;
use common\models\studyplan\StudyplanInvoicesView;
use common\models\studyplan\StudyplanSubject;
use Yii;
use artsoft\controllers\admin\BaseController;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * StudyplanInvoicesController implements the CRUD actions for common\models\studyplan\StudyplanInvoices model.
 */
class DefaultController extends BaseController
{
    public $modelClass = 'common\models\studyplan\StudyplanInvoices';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanInvoicesViewSearch';
    public $modelHistoryClass = 'common\models\history\StudyplanInvoicesHistory';

    public function actionIndex()
    {
        $session = Yii::$app->session;

        $model_date = new DynamicModel(['date_in', 'programm_id', 'education_cat_id', 'course', 'subject_id', 'subject_type_id', 'subject_form_id', 'studyplan_mat_capital_flag', 'studyplan_invoices_status', 'student_id', 'direction_id', 'teachers_id', 'status', 'mat_capital_flag', 'limited_status_id']);
        $model_date->addRule(['date_in'], 'required')
            ->addRule(['date_in', 'student_id', 'programm_id'], 'safe')
            ->addRule(['education_cat_id', 'course', 'subject_id', 'subject_type_id', 'subject_form_id', 'studyplan_mat_capital_flag', 'studyplan_invoices_status', 'direction_id', 'teachers_id', 'status', 'mat_capital_flag', 'limited_status_id'], 'integer');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_invoices_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
            $model_date->programm_id = $session->get('_invoices_programm_id') ?? '' /*EducationProgramm::getProgrammScalar()*/;
            $model_date->education_cat_id = $session->get('_invoices_education_cat_id') ?? '';
            $model_date->course = $session->get('_invoices_course') ?? '';
            $model_date->status = $session->get('_invoices_status') ?? '';
            $model_date->subject_id = $session->get('_invoices_subject_id') ?? '';
            $model_date->subject_type_id = $session->get('_invoices_subject_type_id') ?? '';
            $model_date->limited_status_id = $session->get('_invoices_limited_status_id') ?? '';
            $model_date->subject_form_id = $session->get('_invoices_subject_form_id') ?? '';
            $model_date->studyplan_mat_capital_flag = $session->get('_invoices_studyplan_mat_capital_flag') ?? '';
            $model_date->studyplan_invoices_status = $session->get('_invoices_studyplan_invoices_status') ?? '';
            $model_date->student_id = $session->get('_invoices_student_id') ?? '';
            $model_date->direction_id = $session->get('_invoices_direction_id') ?? '';
            $model_date->teachers_id = $session->get('_invoices_teachers_id') ?? '';
            $model_date->mat_capital_flag = $session->get('_invoices_mat_capital_flag') ?? '';
        }

        $session->set('_invoices_date_in', $model_date->date_in);
        $session->set('_invoices_programm_id', $model_date->programm_id);
        $session->set('_invoices_education_cat_id', $model_date->education_cat_id);
        $session->set('_invoices_course', $model_date->course);
        $session->set('_invoices_status', $model_date->status);
        $session->set('_invoices_subject_id', $model_date->subject_id);
        $session->set('_invoices_subject_type_id', $model_date->subject_type_id);
        $session->set('_invoices_limited_status_id', $model_date->limited_status_id);
        $session->set('_invoices_subject_form_id', $model_date->subject_form_id);
        $session->set('_invoices_studyplan_mat_capital_flag', $model_date->studyplan_mat_capital_flag);
        $session->set('_invoices_studyplan_invoices_status', $model_date->studyplan_invoices_status);
        $session->set('_invoices_student_id', $model_date->student_id);
        $session->set('_invoices_direction_id', $model_date->direction_id);
        $session->set('_invoices_teachers_id', $model_date->teachers_id);
        $session->set('_invoices_mat_capital_flag', $model_date->mat_capital_flag);

        $searchName = StringHelper::basename($this->modelSearchClass::className());
        $searchModel = new $this->modelSearchClass;

        $t = explode(".", $model_date->date_in);
        $date_in = mktime(0, 0, 0, $t[0], 1, $t[1]);

        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $date_in);

        $params = ArrayHelper::merge($this->getParams(), [
            $searchName => [
                'plan_year' => $plan_year,
                'date_in' => $model_date->date_in,
                'programm_id' => $model_date->programm_id,
                'education_cat_id' => $model_date->education_cat_id,
                'course' => $model_date->course,
                'subject_id' => $model_date->subject_id,
                'subject_type_id' => $model_date->subject_type_id,
                'limited_status_id' => $model_date->limited_status_id,
                'subject_form_id' => $model_date->subject_form_id,
                'studyplan_mat_capital_flag' => $model_date->studyplan_mat_capital_flag,
                'studyplan_invoices_status' => $model_date->studyplan_invoices_status,
                'student_id' => $model_date->student_id,
                'direction_id' => $model_date->direction_id,
                'teachers_id' => $model_date->teachers_id,
                'status' => $model_date->status,
                'mat_capital_flag' => $model_date->mat_capital_flag,
            ]
        ]);
//print_r($model_date);die();
        $dataProvider = $searchModel->search($params);
        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel', 'model_date', 'plan_year'));
    }

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $studyplanIds = new DynamicModel(['ids']);
        $studyplanIds->addRule(['ids'], 'safe');

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->status = StudyplanInvoices::STATUS_WORK;
        // $model->invoices_reporting_month = date('m');

//        if (!Yii::$app->request->get('studyplan_id') || !Yii::$app->request->get('ids')) {
//            throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_id.");
//        }
        if (Yii::$app->request->get('ids')) {
            $studyplanIds->ids = array_unique(explode(',', base64_decode(Yii::$app->request->get('ids'))));
        }
        if (Yii::$app->request->get('studyplan_id')) {
            $studyplanIds->ids = [Yii::$app->request->get('studyplan_id')];
        }

        if ($model->load(Yii::$app->request->post()) && $studyplanIds->load(Yii::$app->request->post()) && $model->validate()) {
            $flag = true;
//            echo '<pre>' . print_r($model, true) . '</pre>'; die();
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                foreach ($studyplanIds['ids'] as $item => $studyplan_id) {
                    $m = new $this->modelClass;
                    $m->setAttributes($model->getAttributes());
                    $m->studyplan_id = $studyplan_id;
                    if (!($flag = $m->save(false))) {
                        $transaction->rollBack();
                        break;
                    }
                }
                if ($flag) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                    $this->getSubmitAction($model);
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }

        return $this->renderIsAjax($this->createView, compact('model', 'studyplanIds'));
    }

    public function actionMakeInvoices($id)
    {
        $model = $this->findModel($id);
        return $model->makeDocx();
    }

    public function actionBulkNew()
    {
        $ids = [];
        if (!Yii::$app->request->post('selection', [])) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр POST selection.");
        }
        foreach (Yii::$app->request->post('selection', []) as $index => $val) {
            $t = explode('|', $val);
            $ids[] = $t[0];
        }
        return $this->redirect(['create', 'ids' => base64_encode(implode(',', $ids))]);
    }

    public function actionBulkDelete()
    {
        $ids = [];
        if (!Yii::$app->request->post('selection', [])) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр POST selection.");
        }
        foreach (Yii::$app->request->post('selection', []) as $index => $val) {
            $t = explode('|', $val);
            if ($t[1]) {
                $ids[] = $t[1];
            }
        }
        $modelClass = $this->modelClass;

        foreach ($ids as $id) {
            $where = ['id' => $id];
            $model = $modelClass::findOne($where);
            if ($model) $model->delete();
        }
    }

    public function actionBulkStatus()
    {
        if (!Yii::$app->request->get('status', [])) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр POST status.");
        }

        $status = Yii::$app->request->get('status', []);
        $ids = [];
        if (!Yii::$app->request->post('selection', [])) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр POST selection.");
        }
        foreach (Yii::$app->request->post('selection', []) as $index => $val) {
            $t = explode('|', $val);
            if ($t[1]) {
                $ids[] = $t[1];
            }
        }

        $modelClass = $this->modelClass;
        $where = ['id' => $ids];
        $modelClass::updateAll(['status' => $status], $where);
    }
}