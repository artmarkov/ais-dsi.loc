<?php

namespace artsoft\mailbox;

/**
 * HTML Mailbox Module For Art CMS
 * 
 */
class MailboxModule extends \yii\base\Module
{
    /**
     * Version number of the module.
     */
    const VERSION = '0.1.0';

    public $controllerNamespace = 'artsoft\mailbox\controllers';
    
    public $view;
  
}