<?php

namespace frontend\controllers\execution;

use artsoft\helpers\ArtHelper;
use common\models\execution\ExecutionProgress;
use common\models\studyplan\search\ThematicViewSearch;
use common\models\teachers\PortfolioView;
use common\models\user\UserCommon;
use Yii;
use yii\base\DynamicModel;
use common\models\teachers\Teachers;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;

/**
 * Class DefaultController
 * @package frontend\controllers\execution
 */

class DefaultController extends MainController
{

    public function actionPerform() {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Контроль выполнения планов отдела';
        $id = $this->teachers_id;
        $model_date = $this->modelDate;
        $data = ArtHelper::getStudyYearParams($model_date->plan_year);

        if(Yii::$app->settings->get('mailing.schoolplan_perform_doc')) {
            $query = PortfolioView::find()
                ->where(['signer_id' => Yii::$app->user->identity->getId()])
                ->andWhere(['between', 'datetime_in', $data['timestamp_in'], $data['timestamp_out']]);
        } else {
            $query = PortfolioView::find()
                ->where(['in', 'teachers_id', Teachers::getTeachersForTeacher($this->teachers_id)])
                ->andWhere(['!=', 'teachers_id', $this->teachers_id])
                ->andWhere(['between', 'datetime_in', $data['timestamp_in'], $data['timestamp_out']]);
        }
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $this->renderIsAjax('perform', compact(['dataProvider', 'model_date', 'id']));
    }

    public function actionThematicSign()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Тематические/репертуарные планы на подписи';

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

        $model_date = new DynamicModel(['date_in','teachersIds']);
        $model_date->addRule(['date_in'], 'required')
            ->addRule(['date_in'], 'date', ['format' => 'php:m.Y']);

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');
            $model_date->date_in = $session->get('_progress_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, 1, $year), 'php:m.Y');
        }
        $session->set('_progress_date_in', $model_date->date_in);

        $model_date->teachersIds =  Teachers::find()
            ->select('teachers.id')
            ->joinWith(['user'])
            ->where(['in', 'teachers.id', Teachers::getTeachersForTeacher($this->teachers_id)])
            ->andWhere(['!=', 'teachers.id', $this->teachers_id])
            ->andWhere(['=', 'status', UserCommon::STATUS_ACTIVE])->column();
        $models = ExecutionProgress::getData($model_date);
        $model = $models->getDataTeachers();
        return $this->renderIsAjax('progress', compact('model','model_date'));
    }


}
