<?php

namespace frontend\controllers\reestr;

use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use common\models\schedule\search\ConsultScheduleViewSearch;
use common\models\schedule\search\SubjectScheduleViewSearch;
use common\models\schedule\SubjectScheduleConfirm;
use common\models\service\UsersCard;
use common\models\schedule\SubjectScheduleView;
use common\models\teachers\TeachersActivity;
use common\models\teachers\TeachersLoad;
use common\models\user\UserCommon;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\teachers\Teachers model.
 * $model_date
 */
class TeachersController extends MainController
{

    public $modelClass = 'common\models\teachers\Teachers';
    public $modelSearchClass = 'common\models\teachers\search\TeachersSearch';
    public $modelHistoryClass = 'common\models\history\TeachersHistory';

    public function init()
    {
        $this->viewPath = '@backend/views/teachers/default';
        parent::init();
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id, $readonly = true)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['/reestr/teachers/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $userCommon = UserCommon::findOne(['id' => $model->user_common_id, 'user_category' => UserCommon::USER_CATEGORY_TEACHERS]);
        $userCard = UsersCard::findOne(['user_common_id' => $model->user_common_id]) ?: new UsersCard();
        // $userCommon->scenario = UserCommon::SCENARIO_UPDATE;

        if (!isset($model, $userCommon)) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsActivity = $model->teachersActivity;

        return $this->render('_form', [
            'userCommon' => $userCommon,
            'userCard' => $userCard,
            'model' => $model,
            'modelsActivity' => (empty($modelsActivity)) ? [new TeachersActivity] : $modelsActivity,
            'readonly' => $readonly
        ]);
    }

    /**
     * @param $id
     * @param bool $readonly
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSchedule($id, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['breadcrumbs'][] = 'Расписание занятий';
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }
        $model_date = $this->modelDate;

        return $this->render('schedule', [
            'model' => $model,
            'model_date' => $model_date,
            'readonly' => $readonly
        ]);
    }

    public function actionScheduleItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

            $model_date = $this->modelDate;

            $query = SubjectScheduleView::find()->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($id)])->andWhere(['=', 'plan_year', $model_date->plan_year]);
            $searchModel = new SubjectScheduleViewSearch($query);
            $params = Yii::$app->request->getQueryParams();
            $dataProvider = $searchModel->search($params);
//print_r(Teachers::getTeachersByIds(User::getUsersByRole('signerSchedule')));

            $model_confirm = SubjectScheduleConfirm::find()->where(['=', 'teachers_id', $id])->andWhere(['=', 'plan_year', $model_date->plan_year])->one() ?? new SubjectScheduleConfirm();
            $model_confirm->teachers_id = $id;
            $model_confirm->plan_year = $model_date->plan_year;

            return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel', 'model_date', 'model', 'model_confirm'));

    }

    public function actionConsultItems($id, $objectId = null, $mode = null)
    {
        $modelTeachers = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/teachers', 'Teachers'), 'url' => ['teachers/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['teachers/default/view', 'id' => $id]];
        $this->view->params['tabMenu'] = $this->getMenu($id);

            $model_date = $this->modelDate;

            $query = ConsultScheduleView::find()->where(['=', 'teachers_id', $id])
                ->andWhere(['status' => 1])
                ->andWhere(['=', 'plan_year', $model_date->plan_year]);
            $searchModel = new ConsultScheduleViewSearch($query);
            $params = Yii::$app->request->getQueryParams();
            $dataProvider = $searchModel->search($params);

            $model_confirm = ConsultScheduleConfirm::find()->where(['=', 'teachers_id', $id])->andWhere(['=', 'plan_year', $model_date->plan_year])->one() ?? new ConsultScheduleConfirm();
            $model_confirm->teachers_id = $id;
            $model_confirm->plan_year = $model_date->plan_year;

            return $this->renderIsAjax('consult-items', compact('dataProvider', 'searchModel', 'model_date', 'modelTeachers', 'model_confirm'));

    }

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка', 'url' => ['/reestr/teachers/view', 'id' => $id]],
            ['label' => 'Злементы расписания', 'url' => ['/reestr/teachers/schedule-items', 'id' => $id]],
            ['label' => 'Расписание занятий', 'url' => ['/reestr/teachers/schedule', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/reestr/teachers/consult-items', 'id' => $id]],
        ];
    }
}
