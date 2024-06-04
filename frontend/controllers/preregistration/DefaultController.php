<?php

namespace frontend\controllers\preregistration;

use artsoft\helpers\ArtHelper;
use artsoft\widgets\Notice;
use common\models\education\EntrantPreregistrations;
use common\models\education\EntrantProgramm;
use common\models\forms\FindingForm;
use common\models\forms\RegistrationForm;
use common\models\students\Student;
use Yii;

class DefaultController extends \frontend\controllers\DefaultController
{
    public $freeAccessActions = ['finding', 'registration', 'create'];

    public function init()
    {
        $this->viewPath = '@backend/views/students/default';
        parent::init();
    }

    public function actionFinding()
    {
        $this->view->params['breadcrumbs'][] = 'Запись на обучение';

        $model = new FindingForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $students_id = FindingForm::findByFio($model);
            if ($students_id) {
                Yii::$app->session->setFlash('success', 'Ученик найден в базе. Продолжайте.');
                return $this->redirect(['create', 'id' => $students_id]);
            } else {
                Yii::$app->session->setFlash('info', 'Ученик не найден в базе. Создайте новую запись.');
                return $this->redirect(['registration', 'first_name' => $model->first_name, 'middle_name' => $model->middle_name, 'last_name' => $model->last_name, 'birth_date' => $model->birth_date]);
            }
        }
        return $this->render('finding', compact('model'));
    }

    public function actionRegistration()
    {

        $this->view->params['breadcrumbs'][] = 'Запись на обучение';

        $model = new RegistrationForm(['scenario' => RegistrationForm::SCENARIO_FRONFEND]);

        $model->student_first_name = $_GET['first_name'] ?? null;
        $model->student_middle_name = $_GET['middle_name'] ?? null;
        $model->student_last_name = $_GET['last_name'] ?? null;
        $model->student_birth_date = $_GET['birth_date'] ?? null;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($students_id = $model->registration()) {
                return $this->redirect(['create', 'id' => $students_id]);
            }
//            echo '<pre>' . print_r($model->errors, true) . '</pre>';
//            echo '<pre>' . print_r(Yii::$app->request->post(), true) . '</pre>';
        }
        return $this->render('registration', compact('model'));
    }

    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = 'Запись на обучение';

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new EntrantPreregistrations();
        $model->student_id = $_GET['id'] ?? null;
        $modelStudent = Student::findOne($model->student_id);

        $pre_plan_year = Yii::$app->settings->get('module.pre_plan_year', null);
        $pre_date_start = Yii::$app->settings->get('module.pre_date_start', time());
        $model->plan_year = $pre_plan_year;
        $model->status = EntrantPreregistrations::REG_STATUS_DRAFT;
        $age = ArtHelper::age(Yii::$app->formatter->asTimestamp($modelStudent->userBirthDate), Yii::$app->formatter->asTimestamp($pre_date_start)); // полных лет на начало обучения
        if ($model->load(Yii::$app->request->post())) {
            $entrant_programm_id = $_POST['EntrantPreregistrations']['entrant_programm_id'];
            $model->reg_vid = EntrantProgramm::getEntrantRegVid($entrant_programm_id, $model->plan_year);
            if ($model->save()) {
                Notice::registerSuccess('Вы успешно прошли процедуру записи на обучение.');
                if ($model->sendMessage($modelStudent->userEmail)) {
                    Notice::registerInfo('Проверьте свою почту. Вам отправлено сообщение.');
                }
                return $this->redirect(Yii::$app->homeUrl);
            }
        }
        return $this->renderIsAjax('@frontend/views/preregistration/default/_form',
            [
                'model' => $model,
                'age' => $age['age_year'],
                'plan_year' => $pre_plan_year
            ]);
    }

    public function beforeAction($action)
    {
        $pre_status = Yii::$app->settings->get('module.pre_status', 0);
        $pre_date_in = Yii::$app->formatter->asTimestamp(Yii::$app->settings->get('module.pre_date_in', null));
        $pre_date_out = Yii::$app->formatter->asTimestamp(Yii::$app->settings->get('module.pre_date_out', null));
        if (Yii::$app->user->identity) { // Если по ссылке проходит залогиненный пользователь
            Yii::$app->user->logout();
            $this->redirect(Yii::$app->request->referrer);
        }
        if (!(Yii::$app->user->isGuest && $pre_status == 1 && $pre_date_in < time() && $pre_date_out > time())) {
            $this->redirect('/dashboard');
        }
        return parent::beforeAction($action);
    }
}