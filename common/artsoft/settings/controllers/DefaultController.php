<?php

namespace artsoft\settings\controllers;

/**
 * DefaultController implements General Settings page.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class DefaultController extends SettingsBaseController
{
    public $modelClass = 'artsoft\settings\models\GeneralSettings';
    public $viewPath = '@artsoft/settings/views/default/index';

}