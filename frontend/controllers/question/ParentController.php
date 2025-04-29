<?php

namespace frontend\controllers\question;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\question\Question;
use common\models\question\QuestionAnswers;
use common\models\question\QuestionUsers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * ParentController implements the CRUD actions for common\models\question\Question model.
 */
class ParentController extends \frontend\controllers\DefaultController
{
    public $modelClass = 'common\models\question\Question';
    public $modelSearchClass = 'common\models\question\search\QuestionSearch';

    public $freeAccessActions = ['index', 'new', 'success'];

    public $users_id;

    public function init()
    {
        $this->viewPath = '@frontend/views/question/default';

        parent::init();
    }

    public function actionIndex()
    {
        if (!User::hasRole(['parents'], false)) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $this->view->params['tabMenu'] = $this->tabMenu;
        $subquery = QuestionUsers::find()->select('COUNT(id)')->where('question_id = question.id');
        $query = Question::find()
            ->where(['like', 'users_cat' , Question::GROUP_PARENTS])
            ->andWhere(['<=', 'timestamp_in', time()])
            ->andWhere(['>=', 'timestamp_out', time() - 86400])
            ->andWhere(['=', 'status', Question::STATUS_ACTIVE])
            ->andWhere(['OR',
                ['question_limit' => 0],
                ['IS', 'question_limit', NULL],
                ['>', 'question_limit', $subquery]
            ]);
        $searchModel = false;
        $dataProvider = new ActiveDataProvider(['query' => $query]);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }

    public function actionNew()
    {
        $id = $_GET['id'];
        if (!isset($id)) {
            throw new NotFoundHttpException("The id was not found.");
        }
        /* @var $model \artsoft\db\ActiveRecord */
//        $model = $this->findModel($id);
        $subquery = QuestionUsers::find()->select('COUNT(id)')->where('question_id = question.id');
        $model = Question::find()
            ->where(['like', 'users_cat' , Question::GROUP_PARENTS])
            ->andWhere(['<=', 'timestamp_in', time()])
            ->andWhere(['>=', 'timestamp_out', time() - 86400])
            ->andWhere(['=', 'status', Question::STATUS_ACTIVE])
            ->andWhere(['=', 'id', $id])
            ->andWhere(['OR',
                ['question_limit' => 0],
                ['IS', 'question_limit', NULL],
                ['>', 'question_limit', $subquery]
            ])->one();
        if (!isset($model)) {
            $model = Question::find()
                ->where(['like', 'users_cat' , Question::GROUP_PARENTS])
                ->andWhere(['=', 'id', $id])
                ->one();
            if (isset($model)) {
                $message = 'Форма будет активна ' . Yii::$app->formatter->asDate($model->timestamp_in);
                return $this->renderIsAjax('validate-warning', ['message' => $message]);
            } else {
                throw new NotFoundHttpException("Форма не найдена.");
            }
        }
        $modelUsers = QuestionUsers::find()
            ->where(['question_id' => $id])
            ->andWhere(['users_id' => $this->users_id])
            ->one();
        if (isset($modelUsers)) {
            $message = 'Форма уже была заполнена пользователем.';
            return $this->renderIsAjax('validate-warning', ['message' => $message]);
        }
        $modelVal = new QuestionAnswers(['id' => $id]);
        $modelVal->users_id = $this->users_id;
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['question/student/index']];
        $this->view->params['breadcrumbs'][] = 'Добавление ответа';

        if ($modelVal->load(Yii::$app->request->post()) && $modelVal->save()) {
//                echo '<pre>' . print_r($modelVal, true) . '</pre>';
            $this->redirect(['/question/parent/success/']);
        }
        return $this->renderIsAjax('@backend/views/question/answers/_form', [
            'model' => $modelVal,
            'modelQuestion' => $model,
            'readonly' => false,
        ]);
    }

    public function actionSuccess()
    {
        return $this->renderIsAjax('success');
    }

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) { // Если по ссылке проходит незалогиненный пользователь
            $this->redirect('/auth/default/login');
        } else {
            if (!User::hasRole(['parents'], false)) {
                throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            }
            if (!Yii::$app->user->isGuest) {
                $this->users_id = Yii::$app->user->identity->getId();
            }
        }
        return parent::beforeAction($action);
    }
}