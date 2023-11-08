<?php

namespace artsoft\settings\controllers;

/**
 * MailingController implements Module Settings page.
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class MailingController extends SettingsBaseController
{
    public $modelClass = 'artsoft\settings\models\MailingSettings';
    public $viewPath = '@artsoft/settings/views/mailing/index';

}