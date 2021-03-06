<?php

namespace  common\widgets\fullcalendar\src;

/**
 * Class CoreAsset
 * @package  common\widgets\fullcalendar\src
 */
class CoreAsset extends \yii\web\AssetBundle
{
    /** @var  array Required CSS files for the fullcalendar */
    public $css = [
        'main.css',
    ];

    /** @var  array Required JS files for the fullcalendar */
    public $js = [
        'main.js',
        'locale-all.js',
    ];

    /** @var  array List of the dependencies this assets bundle requires */
    public $depends = [
        'yii\web\YiiAsset',
    ];

    /** @var  string Language for the fullcalendar */
    public $language = null;

    /** @var  string Location of the fullcalendar distribution */
    public $sourcePath = '@common/widgets/fullcalendar/src/lib';

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
