<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\activities\ActivitiesCat;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/calendar', 'Activities Cats');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activities-cat-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton() ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => ActivitiesCat::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'activities-cat-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'activities-cat-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'activities-cat-grid',
                        'dataProvider' => $dataProvider,
//                        'bulkActionOptions' => [
//                            'gridId' => 'activities-cat-grid',
//                            'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
//                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (ActivitiesCat $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                                'options' => ['style' => 'width:20px']
                            ],
                            [
                                'attribute' => 'name',
                                'value' => function (ActivitiesCat $model) {
                                    return $model->name;
                                },
                            ],
                            [
                                'attribute' => 'color',
                                'value' => function (ActivitiesCat $model) {
                                    return '<div style="background-color:' . $model->color . '">&nbsp;</div>';
                                },
                                'format' => 'html',
                            ],
                            'description',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'rendering',
                                'options' => ['style' => 'width:60px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/guidesys/activities-cat',
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


