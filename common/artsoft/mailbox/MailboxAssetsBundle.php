<?php

namespace artsoft\mailbox;

use yii\web\AssetBundle;
use yii\web\View;

class MailboxAssetsBundle extends AssetBundle
{

    public $sourcePath = '@artsoft/mailbox/assets';

    public $css = [
         'css/mailbox.css'      
    ];    
    public $js = [
         'js/mailbox.js'
    ];
    
    /**
     * Registers this asset bundle with a view.
     * @param \yii\web\View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
    public static function register($view)
    {
        $js = <<<JS
            $('[data-toggle="tooltip"]').tooltip()
JS;

        $view->registerJs($js, View::POS_READY);

        return parent::register($view);
    }
}