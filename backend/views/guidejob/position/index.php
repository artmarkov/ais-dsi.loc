<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\guidejob\Position;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/teachers', 'Position');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="position-index">
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
                            /* echo GridQuickLinks::widget([
                                'model' => Position::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?php /*=  GridPageSize::widget(['pjaxId' => 'position-grid-pjax'])*/ ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'position-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'position-grid',
                        'dataProvider' => $dataProvider,
                        'bulkActionOptions' => [
                            'gridId' => 'position-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (Position $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'value' => function (Position $model) {
                                    return $model->name;
                                },
                            ],
                            'slug',
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/guidejob/position',
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


