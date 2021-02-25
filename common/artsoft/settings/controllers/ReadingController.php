<?php

namespace artsoft\settings\controllers;

/**
 * ReadingController implements Reading Settings page.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class ReadingController extends SettingsBaseController
{
    public $modelClass = 'artsoft\settings\models\ReadingSettings';
    public $viewPath = '@artsoft/settings/views/reading/index';

}