<?php

namespace common\widgets\jqueryscheduler;

use yii\web\AssetBundle;

/**
 *
 * ChartPluginAsset
 */
class WeeklySchedulerAsset extends AssetBundle
{
    public $sourcePath = '@common/widgets/jqueryscheduler/schedule/dist';

    public $js = [
        'https://code.jquery.com/ui/1.10.4/jquery-ui.min.js',
        'js/jq.schedule.min.js'

    ];
    public $css = [
        'https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css',
        'css/style.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
