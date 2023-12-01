<?php

namespace frontend\controllers\execution;

use common\models\execution\ExecutionProgress;
use common\models\execution\ExecutionSchoolplanPerform;
use common\models\studyplan\search\ThematicViewSearch;
use Yii;
use yii\base\DynamicModel;
use common\models\teachers\Teachers;
use yii\helpers\StringHelper;

/**
 * Class DefaultController
 * @package frontend\controllers\execution
 */

class DefaultController extends MainController
{

    public function actionPerform() {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Контроль выполнения планов и участия в мероприятиях';

        $model_date = $this->modelDate;
        $models = ExecutionSchoolplanPerform::getData($model_date);
        $model = $models->getDataTeachers();
        return $this->renderIsAjax('perform', compact(['model', 'model_date']));
    }

    public function actionThematicSign()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Индивидуальные планы на подписи';

        $model_date = $this->modelDate;
        $model = Teachers::findOne($this->teachers_id);
        $searchModel = new ThematicViewSearch();

        $searchName = StringHelper::basename($searchModel::className());
        $params = Yii::$app->request->getQueryParams();
        $params[$searchName]['status'] = 1;
        $params[$searchName]['direction_id'] = 1000;
        $params[$searchName]['doc_sign_teachers_id'] = $this->teachers_id;
        $params[$searchName]['plan_year'] = $model_date->plan_year;
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('thematic-sign', compact('dataProvider', 'searchModel',  'model_date', 'model'));
    }

    public function actionProgress()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Контроль заполнения журналов успеваемости';

        $session = Yii::$app->session;

        $model_date = new DynamicModel(['date_in', 'teachers_id']);
        $model_date->addRule(['date_in'], 'required')
            ->addRule(['date_in'], 'date', ['format' => 'php:m.Y'])
            ->addRule('teachers_id', 'integer');

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');
            $model_date->date_in = $session->get('_execution_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
        }
        $session->set('_execution_date_in', $model_date->date_in);

        $models = ExecutionProgress::getData($model_date);
        $model = $models->getDataTeachers();
        return $this->renderIsAjax('progress', compact('model','model_date'));
    }
}
