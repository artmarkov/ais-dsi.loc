<?php

namespace artsoft\settings\controllers;

/**
 * OwnController implements Own Settings page.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class OwnController extends SettingsBaseController
{
    public $modelClass = 'artsoft\settings\models\OwnSettings';
    public $viewPath = '@artsoft/settings/views/own/index';

}