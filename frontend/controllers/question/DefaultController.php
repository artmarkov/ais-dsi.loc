<?php

namespace frontend\controllers\question;

use artsoft\widgets\Notice;
use common\models\question\Question;
use common\models\question\QuestionAnswers;
use common\models\question\search\QuestionSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
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
            ->andWhere(['>=', 'timestamp_out', time() + 86400])
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
        if (!Yii::$app->user->isGuest) {
            $this->redirect('/dashboard');
        }
        return parent::beforeAction($action);
    }

}