<?php

namespace artsoft\settings\controllers;

/**
 * ModuleController implements Module Settings page.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class ModuleController extends SettingsBaseController
{
    public $modelClass = 'artsoft\settings\models\ModuleSettings';
    public $viewPath = '@artsoft/settings/views/module/index';

}