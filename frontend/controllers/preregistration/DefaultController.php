<?php

namespace frontend\controllers\preregistration;

use common\models\forms\FindingForm;
use common\models\forms\RegistrationForm;
use Yii;

/**
 * EntrantProgrammController implements the CRUD actions for common\models\entrant\EntrantProgramm model.
 */
class DefaultController extends \frontend\controllers\DefaultController
{
    public $freeAccessActions = ['finding', 'registration'];

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
                Yii::$app->session->setFlash('success', 'Ученик найден в базе. Продолжайте заполнять форму');
                return $this->redirect(['update', 'id' => $students_id]);
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
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new RegistrationForm();

        $model->student_first_name = $_GET['first_name'] ?? null;
        $model->student_middle_name = $_GET['middle_name'] ?? null;
        $model->student_last_name = $_GET['last_name'] ?? null;
        $model->student_birth_date = $_GET['birth_date'] ?? null;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            echo '<pre>' . print_r($model->errors, true) . '</pre>';
            echo '<pre>' . print_r(Yii::$app->request->post(), true) . '</pre>';
        }
        return $this->render('registration', compact('model'));
    }

    public function beforeAction($action)
    {
        if (!Yii::$app->getUser()->isGuest) {
            $this->redirect('/dashboard');
        }
        return parent::beforeAction($action);
    }
}