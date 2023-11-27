<?php

namespace backend\controllers\execution;

use artsoft\helpers\ArtHelper;
use common\models\education\LessonProgressView;
use common\models\execution\ExecutionProgress;
use common\models\execution\ExecutionSchedule;
use common\models\execution\ExecutionScheduleConsult;
use common\models\execution\ExecutionSchoolplanPerform;
use common\models\execution\ExecutionThematic;
use common\models\schoolplan\SchoolplanPerform;
use common\models\studyplan\Studyplan;
use common\models\teachers\PortfolioView;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use common\models\teachers\Teachers;
use yii\web\NotFoundHttpException;

/**
 * Class DefaultController
 * @package backend\controllers\execution
 */

class DefaultController extends MainController
{
    /**
     * @return mixed|string|\yii\web\Response
     */
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Расписания на подпись';

        $model_date = $this->modelDate;
        $models = ExecutionSchedule::getData($model_date);
        $model = $models->getDataTeachers();
        return $this->renderIsAjax('index', compact('model','model_date'));
    }

    public function actionConsult()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Расписания на подпись';

        $model_date = $this->modelDate;
        $models = ExecutionScheduleConsult::getData($model_date);
        $model = $models->getDataTeachers();
        return $this->renderIsAjax('consult', compact('model','model_date'));
    }

    public function actionPerform() {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Контроль выполнения планов и участия в мероприятиях';

        $model_date = $this->modelDate;
        $models = ExecutionSchoolplanPerform::getData($model_date);
        $model = $models->getDataTeachers();
        return $this->renderIsAjax('perform', compact(['model', 'model_date']));
    }

    public function actionThematic()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Контроль заполнения индивидуальных планов';

        $model_date = $this->modelDate;
        $models = ExecutionThematic::getData($model_date);
        $model = $models->getDataTeachers();
        return $this->renderIsAjax('thematic', compact('model','model_date'));
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
