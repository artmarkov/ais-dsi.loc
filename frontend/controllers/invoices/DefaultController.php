<?php

namespace frontend\controllers\invoices;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\own\Invoices;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanInvoicesView;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\ForbiddenHttpException;

/**
 * StudyplanInvoicesController implements the CRUD actions for common\models\studyplan\StudyplanInvoices model.
 */
class DefaultController extends \frontend\controllers\DefaultController
{
    public $teachers_id;
    public $modelClass = 'common\models\studyplan\StudyplanInvoices';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanInvoicesViewSearch';


    public function init()
    {
        $this->viewPath = '@backend/views/invoices/default';

        if(!User::hasRole(['teacher','department'])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $userId = Yii::$app->user->identity->getId();
        $this->teachers_id = RefBook::find('users_teachers')->getValue($userId) ?? null;
        parent::init();
    }

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $day_in = 1;
        $day_out = date("t");

        $model_date = new DynamicModel(['date_in', 'date_out', 'teachers_id']);
        $model_date->addRule(['date_in', 'date_out'], 'required')
            ->addRule(['date_in', 'date_out'], 'safe')
            ->addRule('date_in', function ($attribute)
            {
                if(Yii::$app->formatter->asTimestamp('01.'.$this->date_in) > Yii::$app->formatter->asTimestamp('01.'.$this->date_out)) $this->addError($attribute, 'Дата начала периода должна быть меньше даты окончания.');
            })
            ->addRule('date_in', function ($attribute)
            {
                $plan_year_1 = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, Yii::$app->formatter->asTimestamp('01.'.$this->date_in));
                $plan_year_2 = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, Yii::$app->formatter->asTimestamp('01.'.$this->date_out));
                if($plan_year_1  != $plan_year_2 ) $this->addError($attribute, 'Задайте период в рамках одного учебного года.');
            });
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $mon = date('m');
            $year = date('Y');

            $model_date->date_in = $session->get('_invoices_date_in') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, $day_in, $year), 'php:m.Y');
            $model_date->date_out = $session->get('_invoices_date_out') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, $day_out, $year), 'php:m.Y');
        }

        $model_date->teachers_id = $this->teachers_id;
        $session->set('_invoices_date_in', $model_date->date_in);
        $session->set('_invoices_date_out', $model_date->date_out);

        $searchName = StringHelper::basename($this->modelSearchClass::className());
        $searchModel = new $this->modelSearchClass;

        $t = explode(".", $model_date->date_in);
        $date_in = mktime(0, 0, 0, $t[0], 1, $t[1]);

        $plan_year = \artsoft\helpers\ArtHelper::getStudyYearDefault(null, $date_in);

        $params = ArrayHelper::merge($this->getParams(), [
            $searchName => [
                'plan_year' => $plan_year,
                'date_in' => $model_date->date_in,
                'date_out' => $model_date->date_out,
                'teachers_id' => $this->teachers_id,
                'status' => StudyplanInvoicesView::STATUS_ACTIVE,
            ]
        ]);
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date', 'plan_year'));
    }



    public function actionMakeInvoices($id)
    {
        $model = $this->findModel($id);
        return $model->makeDocx();
    }

}