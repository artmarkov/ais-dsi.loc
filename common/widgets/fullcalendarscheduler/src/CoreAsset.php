<?php

namespace common\widgets\fullcalendarscheduler\src;

/**
 * Class CoreAsset
 * @package  common\widgets\fullcalendarscheduler\src
 */
class CoreAsset extends \yii\web\AssetBundle
{
    /** @var  array Required CSS files for the fullcalendarscheduler */
    public $css = [
        'main.css',
    ];

    /** @var  array Required JS files for the fullcalendarscheduler */
    public $js = [
        'main.js',
        'locale-all.js',
    ];

    /** @var  array List of the dependencies this assets bundle requires */
    public $depends = [
        'yii\web\YiiAsset',
    ];

    /** @var  string Language for the fullcalendarscheduler */
    public $language = null;

    /** @var  string Location of the fullcalendarscheduler distribution */
    public $sourcePath = '@common/widgets/fullcalendarscheduler/src/lib';

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
