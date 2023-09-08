<?php

namespace backend\controllers\invoices;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use common\models\studyplan\search\StudyplanInvoicesViewSearch;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanInvoicesView;
use common\models\studyplan\StudyplanSubject;
use Yii;
use artsoft\controllers\admin\BaseController;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
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

        $day_in = 1;
        $day_out = date("t");

        $model_date = new DynamicModel(['plan_year','date_in', 'date_out', 'programm_id', 'education_cat_id', 'course', 'subject_id', 'subject_type_id', 'subject_type_sect_id', 'subject_vid_id', 'studyplan_invoices_status', 'student_id', 'direction_id', 'teachers_id']);
        $model_date->addRule(['plan_year','date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'string')
            ->addRule(['plan_year', 'programm_id', 'education_cat_id', 'course', 'subject_id', 'subject_type_id', 'subject_type_sect_id', 'subject_vid_id', 'studyplan_invoices_status', 'student_id', 'direction_id', 'teachers_id'], 'integer');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_invoices_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon-1, $day_in, $year), 'php:d.m.Y');
            $model_date->date_out = $session->get('_invoices_date_out') ?? Yii::$app->formatter->asDate(mktime(23, 59, 59, $mon, $day_out, $year), 'php:d.m.Y');
            $model_date->plan_year = $session->get('_invoices_plan_year') ?? \artsoft\helpers\ArtHelper::getStudyYearDefault();
            $model_date->programm_id = $session->get('_invoices_programm_id') ?? '';
            $model_date->education_cat_id = $session->get('_invoices_education_cat_id') ?? '';
            $model_date->course = $session->get('_invoices_course') ?? '';
            $model_date->subject_id = $session->get('_invoices_subject_id') ?? '';
            $model_date->subject_type_id = $session->get('_invoices_subject_type_id') ?? '';
            $model_date->subject_type_sect_id = $session->get('_invoices_subject_type_sect_id') ?? '';
            $model_date->subject_vid_id = $session->get('_invoices_subject_vid_id') ?? '';
            $model_date->studyplan_invoices_status = $session->get('_invoices_studyplan_invoices_status') ?? '';
            $model_date->student_id = $session->get('_invoices_student_id') ?? '';
            $model_date->direction_id = $session->get('_invoices_direction_id') ?? '';
            $model_date->teachers_id = $session->get('_invoices_teachers_id') ?? '';
        }

        $session->set('_invoices_plan_year', $model_date->plan_year);
        $session->set('_invoices_date_in', $model_date->date_in);
        $session->set('_invoices_date_out', $model_date->date_out);
        $session->set('_invoices_programm_id', $model_date->programm_id);
        $session->set('_invoices_education_cat_id', $model_date->education_cat_id);
        $session->set('_invoices_course', $model_date->course);
        $session->set('_invoices_subject_id', $model_date->subject_id);
        $session->set('_invoices_subject_type_id', $model_date->subject_type_id);
        $session->set('_invoices_subject_type_sect_id', $model_date->subject_type_sect_id);
        $session->set('_invoices_subject_vid_id', $model_date->subject_vid_id);
        $session->set('_invoices_studyplan_invoices_status', $model_date->studyplan_invoices_status);
        $session->set('_invoices_student_id', $model_date->student_id);
        $session->set('_invoices_direction_id', $model_date->direction_id);
        $session->set('_invoices_teachers_id', $model_date->teachers_id);

        $searchName = StringHelper::basename($this->modelSearchClass::className());
        $searchModel = new $this->modelSearchClass;
        $params = ArrayHelper::merge(Yii::$app->request->getQueryParams(), [
            $searchName => [
                'plan_year' => $model_date->plan_year,
                'date_in' => $model_date->date_in,
                'date_out' => $model_date->date_out,
                'programm_id' => $model_date->programm_id,
                'education_cat_id' => $model_date->education_cat_id,
                'course' => $model_date->course,
                'subject_id' => $model_date->subject_id,
                'subject_type_id' => $model_date->subject_type_id,
                'subject_type_sect_id' => $model_date->subject_type_sect_id,
                'subject_vid_id' => $model_date->subject_vid_id,
                'studyplan_invoices_status' => $model_date->studyplan_invoices_status,
                'student_id' => $model_date->student_id,
                'direction_id' => $model_date->direction_id,
                'teachers_id' => $model_date->teachers_id,
                'status' => Studyplan::STATUS_ACTIVE,
            ]
        ]);
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel', 'model_date'));
    }

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $studyplanIds = new DynamicModel(['ids']);
        $studyplanIds->addRule(['ids'], 'safe');

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if (!Yii::$app->request->get('studyplan_id') && !Yii::$app->request->get('ids')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_id.");
        }
        if (Yii::$app->request->get('ids')) {
            $studyplanIds->ids = Yii::$app->request->get('ids');
        }
        if (Yii::$app->request->get('studyplan_id')) {
            $studyplanIds->ids = [Yii::$app->request->get('studyplan_id')];
        }

        if ($model->load(Yii::$app->request->post()) && $studyplanIds->load(Yii::$app->request->post()) && $model->validate()) {
            $flag = true;
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
        if (!Yii::$app->request->post('selection', [])) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр POST selection.");
        }
        $ids = Yii::$app->request->post('selection', []);
        return $this->redirect(['create', 'ids' => $ids]);
    }
}