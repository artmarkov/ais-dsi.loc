<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\DateRangePicker;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\efficiency\TeachersEfficiency;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\efficiency\search\TeachersEfficiencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelTeachers */

?>
<div class="teachers-efficiency-index">
    <div class="panel">
        <div class="panel-heading">
            Показатели эффективности: <?php echo RefBook::find('teachers_fullname')->getValue($modelTeachers->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <?= \artsoft\helpers\ButtonHelper::createButton(isset($modelTeachers->id) ? ['/teachers/default/efficiency', 'id' => $modelTeachers->id, 'mode' => 'create'] : ''); ?>
                    <span class="pull-left">
                        <?php
                        if (\artsoft\Art::isBackend()) {
                            Html::a('<i class="fa fa-bar-chart" aria-hidden="true"></i> График эффективности ', ['/teachers/default/efficiency', 'id' => $modelTeachers->id, 'mode' => 'bar'], ['class' => 'btn btn-sm btn-info']);
                        } else {
                            Html::a('<i class="fa fa-bar-chart" aria-hidden="true"></i> График эффективности ', ['/teachers/efficiency/bar'], ['class' => 'btn btn-sm btn-info']);
                        }
                        ?>
                    </span>
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => TeachersEfficiency::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'teachers-efficiency-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'teachers-efficiency-grid-pjax',
            ])
            ?>
            <?= GridView::widget([
                'id' => 'teachers-efficiency-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => \artsoft\Art::isBackend() ? [
                    'gridId' => 'teachers-efficiency-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ] : false,
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px'], 'visible' => \artsoft\Art::isBackend()],
                    [
                        'attribute' => 'id',
                        'options' => ['style' => 'width:10px'],
                        'value' => function (TeachersEfficiency $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'efficiency_id',
                        'value' => 'efficiencyName',
                        'options' => ['style' => 'width:350px'],
                        'label' => Yii::t('art/guide', 'Efficiency'),
                        'filter' => \common\models\efficiency\EfficiencyTree::getEfficiencyList(),
                    ],
//                                [
//                                    'attribute' => 'teachers_id',
//                                    'value' => 'teachersName',
//                                    'label' => Yii::t('art/teachers', 'Teachers'),
//                                    'filter' => \artsoft\helpers\RefBook::find('teachers_fullname')->getList(),
//                                ],
                    [
                        'attribute' => 'bonus',
                        'value' => function (TeachersEfficiency $model) {
                            return $model->bonus;
                        },
                    ],
                    [
                        'attribute' => 'bonus_vid_id',
                        'value' => function (TeachersEfficiency $model) {
                            return \common\models\efficiency\EfficiencyTree::getBobusVidValue('short', $model->bonus_vid_id);
                        },
                        'filter' => \common\models\efficiency\EfficiencyTree::getBobusVidList('short'),
                    ],
                    [
                        'attribute' => 'date_in',
                        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'autocomplete' => 'off'],
                        'value' => function ($model) {
                            return $model->date_in;
                        },
                        'options' => ['style' => 'width:150px'],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'visible' => \artsoft\Art::isBackend(),
                        'buttons' => [
                            'update' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['/teachers/default/efficiency', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/teachers/default/efficiency', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/teachers/default/efficiency', 'id' => $model->teachers_id, 'objectId' => $model->id, 'mode' => 'delete'], [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'template' => '{view}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'visible' => \artsoft\Art::isFrontend(),
                        'buttons' => [
                            'view' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/teachers/efficiency/view', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>
<?php
if ($searchModel) {
    DateRangePicker::widget([
        'model' => $searchModel,
        'attribute' => 'date_in',
        'format' => 'DD.MM.YYYY',
        'opens' => 'left',
    ]);
}
?>