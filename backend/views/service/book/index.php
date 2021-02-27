<?php

use yii\helpers\Html;
use kartik\grid\GridView;

$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/book/default/create'], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>

<!--    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-sm-9">-->
                <?php
                $colorPluginOptions = [
                    'showPalette' => true,
                    'showPaletteOnly' => true,
                    'showSelectionPalette' => true,
                    'showAlpha' => false,
                    'allowEmpty' => false,
                    'preferredFormat' => 'name',
                    'palette' => [
                        [
                            "white", "black", "grey", "silver", "gold", "brown",
                        ],
                        [
                            "red", "orange", "yellow", "indigo", "maroon", "pink"
                        ],
                        [
                            "blue", "green", "violet", "cyan", "magenta", "purple",
                        ],
                    ]
                ];
                
     $gridColumns = [
                        [
                        'class' => 'kartik\grid\SerialColumn',
                        'contentOptions' => ['class' => 'kartik-sheet-style'],
                        'width' => '36px',
                        'header' => '',
                        'headerOptions' => ['class' => 'kartik-sheet-style']
                        ],
                        [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'width' => '50px',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detail' => function ($model, $key, $index, $column) {
                            return Yii::$app->controller->renderPartial('_form', ['model' => $model]);
                        },
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'expandOneOnly' => true
                        ],
                        [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'name',
                        'pageSummary' => true,
                        'editableOptions' => function ($model, $key, $index) use ($colorPluginOptions) {
                            return [
                                'header' => 'Name',
                                'size' => 'md',
                                'afterInput' => function ($form, $widget) use ($model, $index, $colorPluginOptions) {
                                    return $form->field($model, "color")->widget(\kartik\color\ColorInput::classname(), [
                                                'showDefaultPalette' => false,
                                                'options' => ['id' => "color-{$index}"],
                                                'pluginOptions' => $colorPluginOptions,
                                    ]);
                                }
                                    ];
                                }
                        ],
                        [
                            'attribute' => 'color',
                            'value' => function ($model, $key, $index, $widget) {
                                return "<span class='badge' style='background-color: {$model->color}'> </span>  <code>" . $model->color . '</code>';
                            },
                            'width' => '15%',
                            'filterType' => GridView::FILTER_COLOR,
                            'filterWidgetOptions' => [
                                'showDefaultPalette' => false,
                                'pluginOptions' => $colorPluginOptions,
                            ],
                            'vAlign' => 'middle',
                            'format' => 'raw'
//                                            ,
//                                            'noWrap' => $this->noWrapColor
                        ],
                                        // the buy_amount column configuration
                        [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'buy_amount',
                            'editableOptions' => [
                                'header' => 'Buy Amount',
                                'inputType' => \kartik\editable\Editable::INPUT_SPIN,
                                'options' => ['pluginOptions' => ['min' => 0, 'max' => 5000]]
                            ],
                            'hAlign' => 'right',
                            'vAlign' => 'middle',
                            'width' => '200px',
                            'format' => ['decimal', 2],
                            'pageSummary' => true
                        ],
                    ];
                    ?>
 <?php

        echo GridView::widget([
            'id' => 'kv-grid',
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'headerRowOptions' => ['class' => 'kartik-sheet-style'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style'],
            'pjax' => true, // pjax is set to always true for this demo
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $gridColumns,
            'toolbar' =>  [
                [
               'content' =>
                   Html::button('<i class="fas fa-plus"></i>', [
                       'class' => 'btn btn-success',
                       'title' => Yii::t('art', 'Add Book'),
                       'onclick' => 'alert("This will launch the book creation form.\n\nDisabled for this demo!");'
                   ]) . ' '.
                   Html::a('<i class="fas fa-redo"></i>', ['grid-demo'], [
                       'class' => 'btn btn-outline-secondary',
                       'title'=>Yii::t('art', 'Reset Grid'),
                       'data-pjax' => 0, 
                   ]), 
                   'options' => ['class' => 'btn-group mr-2']
                ],
             '{export}', '{toggleData}',
            ],
             'toggleDataContainer' => ['class' => 'btn-group mr-2'],
    // set export properties
                'export' => [
                    'fontAwesome' => true
                ],
                        'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    //'heading' => $heading,
                ],
             'persistResize' => false,
            'toggleDataOptions' => ['minCount' => 10],
           // 'exportConfig' => $exportConfig,
            'itemLabelSingle' => 'book',
            'itemLabelPlural' => 'books'
        ]);
        ?>


<!--            </div>
        </div>
    </div>-->
</div>  