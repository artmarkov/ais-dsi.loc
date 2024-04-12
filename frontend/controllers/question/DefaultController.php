<?php

namespace frontend\controllers\question;

use common\models\question\Question;
use common\models\question\QuestionAnswers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for common\models\question\Question model.
 */
class DefaultController extends \frontend\controllers\DefaultController
{
    public $modelClass = 'common\models\question\Question';
    public $modelSearchClass = 'common\models\question\search\QuestionSearch';

    public $freeAccessActions = ['index', 'new', 'success'];

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $query = Question::find()->where(['users_cat' => Question::GROUP_GUEST])
            ->andWhere(['<=', 'timestamp_in', time()])
            ->andWhere(['>=', 'timestamp_out', time() - 86400])
            ->andWhere(['=', 'status', Question::STATUS_ACTIVE]);
        $searchModel = false;
        $dataProvider =  new ActiveDataProvider(['query' => $query]);

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }

    public function actionNew()
    {
        $id = $_GET['id'];
        if (!isset($id)) {
            throw new NotFoundHttpException("The id was not found.");
        }
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The Question was not found.");
        }
        $modelVal = new QuestionAnswers(['id' => $id]);

        $this->view->params['breadcrumbs'][] =  ['label' => Yii::t('art/question', 'Questions'), 'url' => ['question/default/index']];
        $this->view->params['breadcrumbs'][] = 'Добавление ответа';

        if ($modelVal->load(Yii::$app->request->post()) && $modelVal->save()) {
//                echo '<pre>' . print_r($modelVal, true) . '</pre>';
            $this->redirect(['/question/default/success/']);
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
        if(Yii::$app->user->identity) { // Если по ссылке проходит залогиненный пользователь
            Yii::$app->user->logout();
            $this->redirect(Yii::$app->request->referrer);
        }
        return parent::beforeAction($action);
    }

}