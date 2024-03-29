<?php

namespace artsoft\settings\controllers;

use artsoft\controllers\admin\BaseController;
use Yii;

/**
 * SettingsBaseController implements base actions for settings pages.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */

abstract class SettingsBaseController extends BaseController
{

    public $tabMenu = [
        ['url' => ['/settings/default/index'], 'label' => 'Основные настройки'],
        ['url' => ['/settings/reading/index'], 'label' => 'Настройки форм'],
        ['url' => ['/settings/own/index'], 'label' => 'Сведения об организации'],
        ['url' => ['/settings/module/index'], 'label' => 'Настройки модулей'],
        ['url' => ['/settings/mailing/index'], 'label' => 'Рассылки и оповещения'],
    ];

    /**
     * Settings model class.
     *
     * @var string
     */
    public $modelClass;

    /**
     * Path to view file for settings.
     *
     * @var string
     */
    public $viewPath;

    /**
     * Action where settings is located
     *
     * @var array
     */
    public $enableOnlyActions = ['index'];

    /**
     * Lists all settings in group.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $modelClass = $this->modelClass;
        $model = new $modelClass();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            Yii::$app->session->setFlash('success', Yii::t('art/settings', 'Your settings have been saved.'));
            return $this->redirect($this->enableOnlyActions);
        }

        return $this->renderIsAjax($this->viewPath, compact('model'));
    }
}