<?php

namespace  backend\widgets\fullcalendar\src;

/**
 * Class CoreAsset
 * @package  backend\widgets\fullcalendar\src
 */
class CoreAsset extends \yii\web\AssetBundle
{
    /** @var  array Required CSS files for the fullcalendar */
    public $css = [
        'main.css',
    ];
    /** @var  array List of the dependencies this assets bundle requires */
    public $depends = [
        'yii\web\YiiAsset',
    ];
    /** @var  array Required JS files for the fullcalendar */
    public $js = [
        'main.js',
        'locale-all.js',
    ];
    /** @var  string Language for the fullcalendar */
    public $language = null;
    /** @var  string Location of the fullcalendar distribution */
    public $sourcePath = '@backend/widgets/fullcalendar/src/lib';

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        $language = empty($this->language) ? \Yii::$app->language : $this->language;
        if (file_exists($this->sourcePath . "/locales/$language.js")) {
            $this->js[] = "locales/$language.js";
        }

        // We need to return the parent implementation otherwise the scripts are not loaded
        return parent::registerAssetFiles($view);
    }
}
