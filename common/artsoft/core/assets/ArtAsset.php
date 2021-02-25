<?php

namespace artsoft\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\View;

/**
 * Class ArtAsset
 * 
 * @package artsoft\core
 */
class ArtAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/admin';

        $this->js = [
            'js/admin.js',
        ];

        $this->css = [
            'css/admin.css',
            'css/widget.css',
            'css/styler.css',
        ];

        $this->depends = [
            JqueryAsset::className(),
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'rmrevin\yii\fontawesome\AssetBundle',
        ];

        parent::init();
    }
    /**
     * Registers this asset bundle with a view.
     * @param \yii\web\View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
//    public static function register($view)
//    {
//        $js = <<<JS
//            $('[data-toggle="tooltip"]').tooltip()
//JS;
//
//        $view->registerJs($js, View::POS_READY);
//
//        return parent::register($view);
//    }
}