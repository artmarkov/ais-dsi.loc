<?php

namespace artsoft\widgets\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class LanguageSelectorAsset
 * 
 * @package artsoft\widgets\assets
 */
class LanguageSelectorAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/source/language-selector';

        $this->css = [
            'css/language-selector.css',
        ];

        $this->depends = [
            JqueryAsset::className(),
        ];

        parent::init();
    }
}