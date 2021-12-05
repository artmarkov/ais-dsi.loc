<?php

namespace common\widgets\weeklyscheduler;

use yii\web\AssetBundle;

/**
 *
 * ChartPluginAsset
 */
class WeeklySchedulerAsset extends AssetBundle
{
    public $sourcePath = '@common/widgets/weeklyscheduler/Dynamic-Weekly-Scheduler-jQuery-Schedule/dist';

    public $js = [
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
        'jquery.schedule.js'

    ];
    public $css = [
        '//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css',
        'jquery.schedule.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
