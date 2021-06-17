<?php

namespace backend\controllers\education;

use common\models\education\EducationProgramm;
use Yii;
use artsoft\controllers\admin\BaseController;
use common\models\history\EducationProgrammHistory;

/**
 * DefaultController implements the CRUD actions for common\models\education\EducationProgramm model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\education\EducationProgramm';
    public $modelSearchClass = 'common\models\education\search\EducationProgrammSearch';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->createView, [
                'model' => $model,
                'readonly' => false
            ]
        );
    }

    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax($this->updateView, [
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new EducationProgrammHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }
}