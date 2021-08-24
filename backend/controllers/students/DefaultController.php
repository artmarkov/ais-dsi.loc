<?php

namespace backend\controllers\students;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use backend\models\Model;
use common\models\history\StudentsHistory;
use common\models\students\StudentDependence;
use common\models\user\UserCommon;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use Yii;


/**
 * DefaultController implements the CRUD actions for common\models\students\Student model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\students\Student';
    public $modelSearchClass = 'common\models\students\search\StudentSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $user = new User();
        $userCommon = new UserCommon();
        $model = new $this->modelClass;
        $modelsDependence = [new StudentDependence(['scenario' => StudentDependence::SCENARIO_PARENT])];

        if ($userCommon->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $modelsDependence = Model::createMultiple(StudentDependence::class);
            Model::loadMultiple($modelsDependence, Yii::$app->request->post());

            // validate all models
            $valid = $userCommon->validate();
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsDependence) && $valid;
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $user->username = $userCommon->generateUsername();
                    $user->generateAuthKey();

                    if (Yii::$app->art->emailConfirmationRequired) {
                        $user->status = User::STATUS_INACTIVE;
                        $user->generateConfirmationToken();
                    }
                    if ($flag = $user->save(false)) {
                        $user->assignRoles(['user', 'student']);
                        $userCommon->user_category = UserCommon::USER_CATEGORY_STUDENTS;
                        $userCommon->user_id = $user->id;
                        if ($flag = $userCommon->save(false)) {
                            $model->user_common_id = $userCommon->id;
                            if ($flag = $model->save(false)) {
                                foreach ($modelsDependence as $modelDependence) {
                                    $modelDependence->student_id = $model->id;
                                    if (!($flag = $modelDependence->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax('create', [
            'userCommon' => $userCommon,
            'model' => $model,
            'modelsDependence' => (empty($modelsDependence)) ? [new StudentDependence] : $modelsDependence,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id);
        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_STUDENTS]);

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsDependence = $model->studentDependence;
        foreach ($modelsDependence as $m) {
            $m->scenario = StudentDependence::SCENARIO_PARENT;
        }
        if ($userCommon->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsDependence, 'id', 'id');
            $modelsDependence = Model::createMultiple(StudentDependence::class, $modelsDependence);
            Model::loadMultiple($modelsDependence, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDependence, 'id', 'id')));

            // validate all models
            $valid = $userCommon->validate();
            $valid = $model->validate() && $valid;
            $valid = Model::validateMultiple($modelsDependence) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $userCommon->save(false)) {
                        if ($flag = $model->save(false)) {
                            if (!empty($deletedIDs)) {
                                StudentDependence::deleteAll(['id' => $deletedIDs]);
                            }
                            foreach ($modelsDependence as $modelDependence) {
                                $modelDependence->student_id = $model->id;
                                if (!($flag = $modelDependence->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'userCommon' => $userCommon,
            'model' => $model,
            'modelsDependence' => (empty($modelsDependence)) ? [new StudentDependence] : $modelsDependence,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }
    /**
     * Удаляет связь студент - родитель
     * Элемент Родитель при это не удаляется
     */
    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $model = $this->findModel($id);
        $data = new StudentsHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }

    public function actionStudentExamination($id)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);
//        $this->view->params['tabMenu'] = $this->tabMenu;
//        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
//        $this->view->params['breadcrumbs'][] = ['label' => 'Сводная таблица', 'url' => ['/efficiency/default/summary']];

        $modelClass = $this->modelClass;
        $searchModel = null;
        $restrictAccess = (ArtHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME)
            && !User::hasPermission($modelClass::getFullAccessPermission()));

        $restrictParams = ($restrictAccess) ? [$modelClass::getOwnerField() => Yii::$app->user->identity->id] : [];
        $dataProvider = new ActiveDataProvider(['query' => $modelClass::find()->where($restrictParams)
            ->andWhere(['=', 'teachers_id', $id]), 'pagination' => false
        ]);

        return $this->renderIsAjax('examination', compact('dataProvider', 'searchModel'));
    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка ученика',  'url' => ['/students/default/update', 'id' => $id]],
            ['label' => 'Индивидуальные планы',  'url' => ['/students/default/student-studyplan', 'id' => $id]],
            ['label' => 'Испытания',  'url' => ['/students/default/student-examination', 'id' => $id]],
            ['label' => 'История',  'url' => ['/students/default/history', 'id' => $id]],
        ];
    }
}