<?php

namespace common\widgets\weeklyscheduler;

use yii\web\AssetBundle;

/**
 *
 * ChartPluginAsset
 */
class WeeklySchedulerAsset extends AssetBundle
{
    public $sourcePath = '@common/widgets/weeklyscheduler/';

    public $js = [
        'https://code.jquery.com/ui/1.10.4/jquery-ui.min.js',
        'schedule/dist/js/jq.schedule.js'

    ];
    public $css = [
        'https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css',
        'schedule/dist/css/style.min.css',
        'css/custom.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
