<?php

use yii\helpers\Html;
use cjtterabytesoft\widget\jvectormap\JvectorMap;

/**
 * This file is part of the CJTTERABYTESOFT yii2-jvectormap
 *
 * (c) CJT TERABYTE LLC yii2-widget <https://github.com/cjtterabytesoft/yii2-jvectormap>
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code
 *
 * @link: https://github.com/cjtterabytesoft/yii2-jvectormap
 * @author: Wilmer Arámbula <cjtterabytellc@gmail.com>
 * @copyright (c) CJT TERABYTE LLC
 * @Widget: [yii2-jvectormap]
 * @Layout [Plugin_JvectorMaps]
 * @since: 0.0.1-dev
 * */
?>
<div class = "panel panel-default dw-widget">
    <div class = "panel-heading">
        <?= Yii::t('art/user', 'Site Visits') ?>
    </div>
    <div class = "panel-body">

        <?=
        JvectorMap::widget([
            'id' => 'vmap',
            'style' => [
                'border' => '1px solid rgba(228, 225, 225, 0.92)',
                'height' => '300px',
                'position' => 'relative',
                'overflow' => 'hidden',
            ],
            /** [map config] * */
            'map' => 'world_mill',
            'backgroundColor' => '#fefefe',
            'markers' => $markers,
            'markersSelectable' => true,
            'markersSelectableOne' => true,
            'markerStyle' => [
                'initial' => [
                    'r' => 4,
                    'fill' => '#fff',
                    'fill-opacity' => 1,
                    'stroke' => '#000',
                    'stroke-width' => 2,
                    'stroke-opacity' => 0.4,
                ],
            ],
            'panOnDrag' => true,
            'regionLabelStyle' => [
                'initial' => [
                    'font-family' => 'Verdana',
                    'font-size' => '12',
                    'font-weight' => 'bold',
                    'cursor' => 'default',
                    'fill' => 'black',
                ],
                'hover' => [
                    'cursor' => 'pointer',
                ],
            ],
            'regionsSelectable' => true,
            'regionsSelectableOne' => true,
            'regionStyle' => [
                'initial' => [
                    'fill' => '#CCCCCC',
                ],
            ],
            'series' => [
                'regions' => [[
                'values' => $values,
                'scale' => ['#dfe7eb', '#4D7686'],
                'normalizeFunction' => 'polynomial',
                    ]],
            ],
            'zoomAnimate' => true,
            'zoomMax' => 8,
            'zoomMin' => 1,
            'zoomOnScroll' => true,
        ]);
        ?>
    </div>
    <div id="heat-fill">
        <span class="fill-a">0</span>
        <span class="fill-b"><?= max($values); ?></span>
    </div>
</div>

<?php
$css = <<<CSS
#heat-fill {

    display: block;
    position: relative;
    height: 7px;
    width: 200px;
    background: -webkit-linear-gradient(to right, #dfe7eb 20%, #4D7686 80%); /* Safari 5.1, iOS 5.0-6.1, Chrome 10-25, Android 4.0-4.3 */
    background: -moz-linear-gradient(to right, #dfe7eb 20%, #4D7686 80%); /* Firefox 3.6-15 */
    background: -o-linear-gradient(to right, #dfe7eb 20%, #4D7686 80%); /* Opera 11.1-12 */
    background: linear-gradient(to right, #dfe7eb 20%, #4D7686 80%); /* Opera 15+, Chrome 25+, IE 10+, Firefox 16+, Safari 6.1+, iOS 7+, Android 4.4+ */
}
.fill-a, .fill-b {

    width: 20px;
    text-align: right;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    background: #fff;
    padding-right: 4px;
    position: absolute;
    left: 0;
    margin-top: -7px;
    font-weight: 700;

}
.fill-b {
    text-align: left;
    position: absolute;
    right: 0;
    left: auto;
    top: 0;
    width: 60px;
    padding-left: 4px;
    padding-right: 0;
}

CSS;

$this->registerCss($css);
?>