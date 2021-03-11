<?php

namespace artsoft\settings\controllers;

/**
 * ReadingController implements Reading Settings page.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class OwnController extends SettingsBaseController
{
    public $modelClass = 'artsoft\settings\models\OwnSettings';
    public $viewPath = '@artsoft/settings/views/own/index';

}