<?php

namespace frontend\controllers\help;

use frontend\models\SupportForm;
use Yii;

/**
 * HelpController implements the CRUD actions for common\models\guidesys\HelpTree model.
 */
class GuideHelpController extends \frontend\controllers\DefaultController
{
    public $modelClass = 'common\models\guidesys\HelpTree';
    public $modelSearchClass = '';

    public function actionCheck()
    {
        $key = Yii::$app->request->post('key');
        $model = $this->modelClass::find()->where(['id' => $key])->one();
        return $this->renderAjax('result', [
            'model' => $model
        ]);
    }
}