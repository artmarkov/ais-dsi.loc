<?php

use yii\widgets\Pjax;
use artsoft\grid\SortableGridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\PieceCategory;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\PieceCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Piece Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="piece-category-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            echo GridQuickLinks::widget([
                                'model' => PieceCategory::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'piece-category-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'piece-category-grid-pjax',
                    ])
                    ?>

                    <?=
                    SortableGridView::widget([
                        'id' => 'piece-category-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'sortableAction' => ['grid-sort'],
                        'bulkActionOptions' => [
                            'gridId' => 'piece-category-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (PieceCategory $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            'name',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [PieceCategory::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [PieceCategory::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:60px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/guidestudy/piece-category',
                                'template' => '{update} {delete}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],

                            ],

                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>


