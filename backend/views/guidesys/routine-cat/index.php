
<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use common\models\routine\RoutineCat;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/routine', 'Routine Cats');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="routine-cat-index">
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
                              'model' => RoutineCat::className(),
                              'searchModel' => $searchModel,
                              ]) */
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'routine-cat-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'routine-cat-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'routine-cat-grid',
                        'dataProvider' => $dataProvider,
                        'bulkActionOptions' => [
                            'gridId' => 'routine-cat-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (RoutineCat $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'options' => ['style' => 'width:300px'],
                                'attribute' => 'name',
                                'value' => function (RoutineCat $model) {
                                    return $model->name;
                                },
                            ],
                            [
                                'attribute' => 'color',
                                'value' => function(RoutineCat $model){
                                    return '<div style="background-color:' . $model->color . '">&nbsp;</div>';
                                },
                                'format' => 'html',
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'vacation_flag',
                                'optionsArray' => [
                                    [1, Yii::t('art', 'Yes'), 'primary'],
                                    [0, Yii::t('art', 'No'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'dayoff_flag',
                                'optionsArray' => [
                                    [1, Yii::t('art', 'Yes'), 'primary'],
                                    [0, Yii::t('art', 'No'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/guidesys/routine-cat',
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


