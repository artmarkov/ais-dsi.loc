<?php

namespace artsoft\user\controllers;

use artsoft\models\User;
use http\Url;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for User model.
 */
class DefaultController extends MainController
{
    /**
     * @var User
     */
    public $modelClass = 'artsoft\models\User';

    /**
     * @var UserSearch
     */
    public $modelSearchClass = 'artsoft\user\models\search\UserSearch';

    public $disabledActions = ['view'];
    
    /**
     * @return mixed|string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new User(['scenario' => 'newUser']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->renderIsAjax('create', compact('model'));
    }

    /**
     * @param int $id User ID
     *
     * @throws \yii\web\NotFoundHttpException
     * @return string
     */
    public function actionChangePassword($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = User::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('art/user', 'User not found'));
        }

        $model->scenario = 'changePassword';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art/auth', 'Password has been updated'));
            return $this->redirect(['change-password', 'id' => $model->id]);
        }

        return $this->renderIsAjax('changePassword', compact('model'));
    }

    public function actionHistory($id)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/user', $model->username), 'url' => ['/user/default/update', 'id' => $model->id]];
        $this->view->title = 'История изменений: ' . $model->username;
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $data = new \common\history\UserHistory($id);
        $dataProvider = $data->search(Yii::$app->request->get());

        $content = \Yii::$app->view->renderFile('@common/history/views/history.php', [
            'dataProvider' => $dataProvider,
            'filterModel' => $data,
        ]);
        return $this->renderContent($content);
    }

}