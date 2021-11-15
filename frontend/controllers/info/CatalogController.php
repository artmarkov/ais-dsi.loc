<?php

namespace frontend\controllers\info;

use artsoft\models\User;
use frontend\models\SupportForm;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * HelpController implements the CRUD actions for common\models\guidesys\HelpTree model.
 */
class CatalogController extends \frontend\controllers\DefaultController
{
    public $modelClass = 'common\models\info\FilesCatalog';
    public $modelSearchClass = '';

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Каталог файлов';
        return $this->render('index-view');
    }

    public function actionEdit()
    {
        if (!$this->modelClass::getEditAllow()) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = ['label' => 'Каталог файлов', 'url' => ['/info/catalog']];;
        $this->view->params['breadcrumbs'][] = 'Редактировать';
        $this->viewPath = '@backend/views/info/catalog';
        return $this->render('index');
    }

    public function actionCheck()
    {
        $key = Yii::$app->request->post('key');
        $model = $this->modelClass::find()->where(['id' => $key])->one();
        return $this->renderAjax('result', [
            'model' => $model
        ]);
    }
}