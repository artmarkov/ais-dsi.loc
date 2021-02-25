<?php

namespace artsoft\widgets\assets;

use yii\web\AssetBundle;

class ScrollupAsset extends AssetBundle {

    public function init() {
        $this->sourcePath = __DIR__ . '/source/scrollup';

        $this->css = [
            'css/scrollup.css',
        ];
        $this->js = [
            'js/scrollup.js',
        ];

        $this->depends = [
            'yii\web\YiiAsset',
        ];

        parent::init();
    }

}
