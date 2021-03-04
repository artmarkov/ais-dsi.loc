<?php

namespace backend\widgets\fullcalendarscheduler\src;

/**
 * Class PrintAsset
 * @package backend\widgets\fullcalendarscheduler\src
 */
class PrintAsset extends \yii\web\AssetBundle
{
    /** @var  array The CSS file for the print style */
    public $css = [
        'fullcalendar.print.css',
    ];
    /** @var  array The CSS options */
    public $cssOptions = [
        'media' => 'print',
    ];
    /** @var  string bower path for the print settings */
    public $sourcePath = '@bower/fullcalendar/dist';
}

