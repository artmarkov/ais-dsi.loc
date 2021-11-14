<?php

namespace frontend\controllers\info;

use artsoft\models\User;
use frontend\models\SupportForm;
use Yii;

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

        if (User::hasPermission('editCatalog') || Yii::$app->user->isSuperadmin) {
            $this->viewPath = '@backend/views/info/catalog';
            return $this->render('index');
        }
        return $this->render('index-view');
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