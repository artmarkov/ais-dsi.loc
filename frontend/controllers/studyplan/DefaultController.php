<?php

namespace frontend\controllers\studyplan;

use common\models\education\LessonProgressView;
use common\models\schedule\search\ConsultScheduleStudyplanViewSearch;
use common\models\schedule\search\SubjectScheduleStudyplanViewSearch;
use common\models\schoolplan\SchoolplanPerform;
use common\models\schoolplan\search\SchoolplanPerformSearch;
use common\models\schoolplan\search\SchoolplanProtocolViewSearch;
use common\models\students\Student;
use common\models\studyplan\search\StudyplanInvoicesViewSearch;
use common\models\studyplan\search\StudyplanThematicViewSearch;
use common\models\studyplan\search\SubjectCharacteristicViewSearch;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanInvoices;
use common\models\studyplan\StudyplanSubject;
use common\models\studyplan\StudyplanThematic;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

/**
 * DefaultController
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\studyplan\Studyplan';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanViewSearch';

    public function actionIndex()
    {
        $modelSearchClass = $this->modelSearchClass;
        $model_date = $this->modelDate;

        $searchName = StringHelper::basename($modelSearchClass::className());
        $searchModel = new $modelSearchClass;
        $teachers_id = null;
        $params = ArrayHelper::merge(Yii::$app->request->getQueryParams(), [
            $searchName => [
                'plan_year' => $model_date->plan_year,
                'student_id' => $this->student_id,
            ]
        ]);
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date', 'teachers_id'));
    }

    public function actionView($id)
    {
        $model = Studyplan::findOne($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }
        $model_date = $this->modelDate;

        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }
        $modelsStudyplanSubject = $model->studyplanSubject;

        return $this->render('update', [
            'model' => $model,
            'modelsStudyplanSubject' => (empty($modelsStudyplanSubject)) ? [new StudyplanSubject] : $modelsStudyplanSubject,
            'model_date' => $model_date,
            'readonly' => true
        ]);
    }

    public function actionScheduleItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model_date = $this->modelDate;

        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }
        $searchModel = new SubjectScheduleStudyplanViewSearch();

        $searchName = StringHelper::basename($searchModel::className());
        $params = Yii::$app->request->getQueryParams();
        $params[$searchName]['studyplan_id'] = $id;
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));

    }

    public function actionSchedule($id, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }

        $model_date = $this->modelDate;

        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }

        return $this->render('schedule', [
            'model' => $model,
            'model_date' => $model_date,
            'readonly' => $readonly
        ]);
    }

    public function actionConsultItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $model_date = $this->modelDate;

        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }

        $searchModel = new ConsultScheduleStudyplanViewSearch();

        $searchName = StringHelper::basename($searchModel::className());
        $params = Yii::$app->request->getQueryParams();
        $params[$searchName]['studyplan_id'] = $id;
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('consult-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));

    }

    public function actionCharacteristicItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $model_date = $this->modelDate;

        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }

        $searchModel = new SubjectCharacteristicViewSearch();

        $searchName = StringHelper::basename($searchModel::className());
        $params = Yii::$app->request->getQueryParams();
        $params[$searchName]['studyplan_id'] = $id;
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('characteristic-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));

    }

    public function actionThematicItems($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['studyplan/default/thematic-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = StudyplanThematic::findOne($objectId);
            if (!isset($model)) {
                throw new NotFoundHttpException("The StudyplanThematic was not found.");
            }
            $modelsItems = $model->studyplanThematicItems;

            return $this->renderIsAjax('@backend/views/studyplan/studyplan-thematic/_form.php', [
                'model' => $model,
                'modelsItems' => (empty($modelsItems)) ? [new StudyplanThematicItems] : $modelsItems,
                'readonly' => $readonly
            ]);

        } else {
            $model_date = $this->modelDate;

            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }
            $searchModel = new StudyplanThematicViewSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('thematic-items', compact('dataProvider', 'searchModel', 'model', 'model_date'));
        }
    }

    public function actionStudyplanProgress($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);


        $session = Yii::$app->session;

        $model_date = new DynamicModel(['date_in']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'safe')
            ->addRule('date_in', function ($attribute)
            {
                if(Yii::$app->formatter->asTimestamp('01.'.$this->date_in) > Yii::$app->formatter->asTimestamp('01.'.$this->date_out)) $this->addError($attribute, 'Дата начала периода должна быть меньше даты окончания.');
            })
            ->addRule('date_in', function ($attribute)
            {
                $plan_year_1 = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, Yii::$app->formatter->asTimestamp('01.'.$this->date_in));
                $plan_year_2 = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, Yii::$app->formatter->asTimestamp('01.'.$this->date_out));
                if($plan_year_1  != $plan_year_2 ) $this->addError($attribute, 'Задайте период в рамках одного учебного года.');
            });


        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
            $model_date->date_out = $session->get('_progress_date_out') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, date("t"), $year), 'php:m.Y');
        }
        $session->set('_progress_date_in', $model_date->date_in);
        $session->set('_progress_date_out', $model_date->date_out);

        $modelLessonProgress = LessonProgressView::getDataStudyplan($model_date, $id, true);

        if (Yii::$app->request->post('submitAction') == 'excel') {
            // TeachersEfficiency::sendXlsx($data);
        }

        return $this->renderIsAjax('studyplan-progress', [
            'model' => $modelLessonProgress,
            'model_date' => $model_date,
            'modelStudent' => $model
        ]);

    }

    public function actionStudyplanPerform($id, $objectId = null, $mode = null, $readonly = true)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

            $searchModel = new SchoolplanPerformSearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = $this->getParams();
            $params[$searchName]['class'] = \yii\helpers\StringHelper::basename(get_class($model));
            $params[$searchName]['studyplan_id'] = $id;
            $dataProvider = $searchModel->search($params);
        $model_date = $this->modelDate;

        if (!isset($model_date)) {
            throw new NotFoundHttpException("The model_date was not found.");
        }

        return $this->renderIsAjax('perform-items', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'model' => $model, 'model_date' => $model_date]);
    }

    public function actionStudyplanInvoices($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ('view' == $mode && $objectId) {
            $modelStudyplanInvoices = StudyplanInvoices::findOne($objectId);
            return $this->renderIsAjax('@backend/views/invoices/default/view.php', [
                'model' => $modelStudyplanInvoices,
            ]);

        }
        if ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Invoices'), 'url' => ['studyplan/default/studyplan-invoices', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $modelStudyplanInvoices = StudyplanInvoices::findOne($objectId);
            if (!isset($modelStudyplanInvoices)) {
                throw new NotFoundHttpException("The StudyplanInvoices was not found.");
            }

            if ($modelStudyplanInvoices->load(Yii::$app->request->post()) AND $modelStudyplanInvoices->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction($modelStudyplanInvoices);
            }

            return $this->renderIsAjax('@backend/views/invoices/default/_form.php', [
                // 'model' => $model,
                'model' => $modelStudyplanInvoices,
                'readonly' => false,
            ]);

        } else {
            $session = Yii::$app->session;

            $model_date = $this->modelDate;

            if (!isset($model_date)) {
                throw new NotFoundHttpException("The model_date was not found.");
            }
            if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
                $model_date->studyplan_id = $id;
            }
            if ($model_date->studyplan_id != $id) {
                $this->redirect(['/studyplan/default/' . $model_date->studyplan_id . '/studyplan-invoices']);
            }
            $session->set('_invoices_studyplan_id', $model_date->studyplan_id);

            $searchModel = new StudyplanInvoicesViewSearch();
            $searchName = StringHelper::basename($searchModel::className());
            $params = ArrayHelper::merge(Yii::$app->request->getQueryParams(), [
                $searchName => [
                    'studyplan_id' => $model_date->studyplan_id,
                    'plan_year' => $model_date->plan_year,
                    'status' => Studyplan::STATUS_ACTIVE,
                ]
            ]);
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('invoices-items', compact('dataProvider', 'searchModel', 'model_date', 'model'));
        }
    }

    public function actionStudentsView($id)
    {
        $model = $this->findModel($id);
        $modelStudent = Student::findOne($model->student_id);
        $studentDependence = $modelStudent->studentDependence;
        $this->view->params['breadcrumbs'][] = ['label' => 'Карточка ученика'];
        $this->view->params['tabMenu'] = $this->getMenu($id);
        return $this->renderIsAjax('students_view', compact('modelStudent', 'studentDependence'));
    }

    public function actionMakeInvoices($id)
    {
        $model = StudyplanInvoices::findOne($id);
        return $model->makeDocx();
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка ученика', 'url' => ['/studyplan/default/students-view', 'id' => $id]],
            ['label' => 'Карточка пплана учащегося', 'url' => ['/studyplan/default/view', 'id' => $id]],
            ['label' => 'Нагрузка', 'url' => ['/studyplan/default/load-items', 'id' => $id]],
            ['label' => 'Элементы расписания', 'url' => ['/studyplan/default/schedule-items', 'id' => $id]],
            ['label' => 'Расписание занятий', 'url' => ['/studyplan/default/schedule', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/studyplan/default/consult-items', 'id' => $id]],
            ['label' => 'Характеристики по предметам', 'url' => ['/studyplan/default/characteristic-items', 'id' => $id]],
            ['label' => 'Тематические/репертуарные планы', 'url' => ['/studyplan/default/thematic-items', 'id' => $id]],
            ['label' => 'Дневник успеваемости', 'url' => ['/studyplan/default/studyplan-progress', 'id' => $id]],
            ['label' => 'Оплата за обучение', 'url' => ['/studyplan/default/studyplan-invoices', 'id' => $id]],
            ['label' => 'Выполнение плана и участие в мероприятиях', 'url' => ['/studyplan/default/studyplan-perform', 'id' => $id]],
        ];
    }
}