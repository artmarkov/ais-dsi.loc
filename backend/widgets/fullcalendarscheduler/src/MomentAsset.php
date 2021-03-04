<?php

namespace backend\widgets\fullcalendarscheduler\src;

/**
 * Class MomentAsset
 * @package backend\widgets\fullcalendarscheduler\src
 */
class MomentAsset extends \yii\web\AssetBundle
{
    /** @var  array  The javascript file for the Moment library */
    public $js = [
        'moment.js',
    ];
    /** @var  string  The location of the Moment.js library */
    public $sourcePath = '@bower/moment';
}