<?php

namespace frontend\controllers\invoices;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\studyplan\Studyplan;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\ForbiddenHttpException;

/**
 * StudyplanInvoicesController implements the CRUD actions for common\models\studyplan\StudyplanInvoices model.
 */
class DefaultController extends \frontend\controllers\DefaultController
{
    public $teachers_id;
    public $modelClass = 'common\models\studyplan\StudyplanInvoices';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanInvoicesViewSearch';


    public function init()
    {
        $this->viewPath = '@backend/views/invoices/default';

        if(!User::hasRole(['teacher','department'])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $userId = Yii::$app->user->identity->getId();
        $this->teachers_id = RefBook::find('users_teachers')->getValue($userId) ?? null;
        parent::init();
    }

    public function actionIndex()
    {
        $session = Yii::$app->session;

        $model_date = new DynamicModel(['date_in', 'programm_id', 'course', 'subject_id', 'subject_type_id', 'subject_type_sect_id', 'subject_vid_id', 'subject_form_id', 'studyplan_invoices_status', 'student_id', 'direction_id', 'teachers_id']);
        $model_date->addRule(['date_in'], 'required')
            ->addRule(['date_in'], 'safe')
            ->addRule(['programm_id', 'course', 'subject_id', 'subject_type_id', 'subject_type_sect_id', 'subject_vid_id', 'subject_form_id', 'studyplan_invoices_status', 'student_id', 'direction_id', 'teachers_id'], 'integer');
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_invoices_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
          //  $model_date->programm_id = $session->get('_invoices_programm_id') ?? EducationProgramm::getProgrammScalar();
//            $model_date->education_cat_id = $session->get('_invoices_education_cat_id') ?? '';
            $model_date->course = $session->get('_invoices_course') ?? '';
            $model_date->subject_id = $session->get('_invoices_subject_id') ?? '';
            $model_date->subject_type_id = $session->get('_invoices_subject_type_id') ?? '';
            $model_date->subject_type_sect_id = $session->get('_invoices_subject_type_sect_id') ?? '';
            $model_date->subject_vid_id = $session->get('_invoices_subject_vid_id') ?? '';
            $model_date->subject_form_id = $session->get('_invoices_subject_form_id') ?? '';
            $model_date->studyplan_invoices_status = $session->get('_invoices_studyplan_invoices_status') ?? '';
            $model_date->student_id = $session->get('_invoices_student_id') ?? '';
//            $model_date->direction_id = $session->get('_invoices_direction_id') ?? '';
//            $model_date->teachers_id = $session->get('_invoices_teachers_id') ?? '';
        }

        $session->set('_invoices_date_in', $model_date->date_in);
//        $session->set('_invoices_programm_id', $model_date->programm_id);
//        $session->set('_invoices_education_cat_id', $model_date->education_cat_id);
        $session->set('_invoices_course', $model_date->course);
        $session->set('_invoices_subject_id', $model_date->subject_id);
        $session->set('_invoices_subject_type_id', $model_date->subject_type_id);
        $session->set('_invoices_subject_type_sect_id', $model_date->subject_type_sect_id);
        $session->set('_invoices_subject_vid_id', $model_date->subject_vid_id);
        $session->set('_invoices_subject_form_id', $model_date->subject_form_id);
        $session->set('_invoices_studyplan_invoices_status', $model_date->studyplan_invoices_status);
        $session->set('_invoices_student_id', $model_date->student_id);
//        $session->set('_invoices_direction_id', $model_date->direction_id);
//        $session->set('_invoices_teachers_id', $model_date->teachers_id);

        $searchName = StringHelper::basename($this->modelSearchClass::className());
        $searchModel = new $this->modelSearchClass;

        $t = explode(".", $model_date->date_in);
        $date_in = mktime(0, 0, 0, $t[0], 1, $t[1]);

        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $date_in);

        $params = ArrayHelper::merge($this->getParams(), [
            $searchName => [
                'plan_year' => $plan_year,
                'date_in' => $model_date->date_in,
//                'programm_id' => $model_date->programm_id,
//                'education_cat_id' => $model_date->education_cat_id,
                'course' => $model_date->course,
                'subject_id' => $model_date->subject_id,
                'subject_type_id' => $model_date->subject_type_id,
                'subject_type_sect_id' => $model_date->subject_type_sect_id,
                'subject_vid_id' => $model_date->subject_vid_id,
                'subject_form_id' => $model_date->subject_form_id,
                'studyplan_invoices_status' => $model_date->studyplan_invoices_status,
                'student_id' => $model_date->student_id,
//                'direction_id' => $model_date->direction_id,
                'teachers_id' => $this->teachers_id,
                'status' => Studyplan::STATUS_ACTIVE,
            ]
        ]);
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date'));
    }



    public function actionMakeInvoices($id)
    {
        $model = $this->findModel($id);
        return $model->makeDocx();
    }

}