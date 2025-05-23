<?php

namespace frontend\controllers\question;

use common\models\question\Question;
use common\models\question\QuestionAnswers;
use common\models\question\QuestionUsers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\question\Question model.
 */
class DefaultController extends \frontend\controllers\DefaultController
{
    public $modelClass = 'common\models\question\Question';
    public $modelSearchClass = 'common\models\question\search\QuestionSearch';

    public $freeAccessActions = ['index', 'new', 'success', 'validate', 'validate-success', 'validate-warning'];

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $subquery = QuestionUsers::find()->select('COUNT(id)')->where('question_id = question.id');
        $query = Question::find()
            ->where(new \yii\db\Expression("0 = any (string_to_array(users_cat, ',')::int[])"))
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
            ->where(['like', 'users_cat' , Question::GROUP_GUEST])
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
                ->where(['like', 'users_cat' , Question::GROUP_GUEST])
                ->andWhere(['=', 'id', $id])
                ->one();
            if (isset($model)) {
                $message = 'Форма будет активна ' . Yii::$app->formatter->asDate($model->timestamp_in);
                return $this->renderIsAjax('validate-warning', ['message' => $message]);
            } else {
                throw new NotFoundHttpException("Форма не найдена.");
            }
        }
        $modelVal = new QuestionAnswers(['id' => $id]);

        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['question/default/index']];
        $this->view->params['breadcrumbs'][] = 'Добавление ответа';

        if ($modelVal->load(Yii::$app->request->post()) && $modelVal->save()) {
//                echo '<pre>' . print_r($modelVal, true) . '</pre>';
            $this->redirect(['/question/default/success']);
        }
        return $this->renderIsAjax('@backend/views/question/answers/_form', [
            'model' => $modelVal,
            'modelQuestion' => $model,
            'readonly' => false,
        ]);
    }

    public function actionValidate()
    {
        $token = json_decode(base64_decode($_GET['token']), true);
        $id = $token['id'];
        $user_id = $token['user_id'];
        $modelQuestion = Question::findOne(['id' => $id]);

        if (!$modelQuestion->isModerator(Yii::$app->user->identity->getId())) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model = QuestionUsers::findOne(['id' => $user_id]);
        if ($model) {
            if ($model->read_flag == QuestionUsers::READ_OFF) {
                $model->read_flag = QuestionUsers::READ_ON;
                $model->save(false);
                $this->redirect(['/question/default/validate-success']);
            } else {
                $this->redirect(['/question/default/validate-warning']);
            }
        } else {
            Yii::$app->session->setFlash('danger', 'Токен не найден!');
            $this->redirect(Yii::$app->homeUrl);
        }
    }

    public function actionSuccess()
    {
        return $this->renderIsAjax('success');
    }

    public function actionValidateSuccess()
    {
        return $this->renderIsAjax('validate-success');
    }

    public function actionValidateWarning()
    {
        return $this->renderIsAjax('validate-warning', ['message' => 'Попытка повторного прохода.']);
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, ['validate', 'validate-success', 'validate-warning'])) {
            if (Yii::$app->user->identity) { // Если по ссылке проходит залогиненный пользователь
                Yii::$app->user->logout();
                $this->redirect(Yii::$app->request->referrer);
            }
        } else {
            if (Yii::$app->user->isGuest) { // Если по ссылке проходит незалогиненный пользователь
                $this->redirect('/auth/default/login');
            }
        }
        return parent::beforeAction($action);
    }

}