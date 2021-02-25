<?php

/**
 * Created by PhpStorm.
 * User: phpNT - http://phpnt.com
 * Date: 01.04.2017
 * Time: 13:47
 */

namespace artsoft\widgets\assets;

use yii\web\AssetBundle;

/**
 * Class AssetBundle
 */
class PaceAsset extends AssetBundle {

    /**
     * @inherit
     */
    public $sourcePath = '@bower/pace';

    /**
     * @inherit
     */
    public $js = [
        'pace.min.js',
    ];
    public $depends = [
        'artsoft\widgets\assets\PaceCssAsset',
    ];

    public function init() {
        parent::init();
    }

}
