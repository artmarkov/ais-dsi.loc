<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\routine\Routine;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\routine\search\RoutineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/routine', 'Routines');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="routine-index">
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
                                'model' => Routine::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'routine-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'routine-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'routine-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'routine-grid',
                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'cat_id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/routine/default',
                                'label' => Yii::t('art', 'Name'),
                                'title' => function (Routine $model) {
                                    return Html::a($model->cat->name, ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'filter' => \common\models\routine\RoutineCat::getCatList(),
                                'buttonsTemplate' => '{update} {delete}',
                            ],
                            'description',
                            [
                                'class' => 'artsoft\grid\columns\DateFilterColumn',
                                'attribute' => 'start_date',
                                'value' => function (Routine $model) {
                                    return '<span style="font-size:85%;" class="label label-'
                                        . ((time() >= \Yii::$app->formatter->asTimestamp($model->start_date)) ? 'danger' : 'success') . '">'
                                        . $model->start_date . '</span>';
                                },
                                'label' => Yii::t('art/routine', 'Start Date'),
                                'format' => 'raw',
                                'options' => ['style' => 'width:150px'],
                            ],
                            [
                                'class' => 'artsoft\grid\columns\DateFilterColumn',
                                'attribute' => 'end_date',
                                'value' => function (Routine $model) {
                                    return '<span style="font-size:85%;" class="label label-'
                                        . ((time() >= \Yii::$app->formatter->asTimestamp($model->end_date)) ? 'danger' : 'success') . '">'
                                        . $model->end_date . '</span>';
                                },
                                'label' => Yii::t('art/routine', 'End Date'),
                                'format' => 'raw',
                                'options' => ['style' => 'width:150px'],
                            ],
                            [
                                     'attribute' => 'start_date',
                                    'value' => function (Routine $model) {
                                        return $model->start_date;
                                    },
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
