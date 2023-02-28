<?php

use artsoft\helpers\RefBook;
use common\models\education\LessonTest;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\entrant\GuideEntrantTest;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\entrant\search\GuideEntrantTestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Guide Entrant Tests');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guide-entrant-test-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    echo GridQuickLinks::widget([
                        'model' => GuideEntrantTest::className(),
                        'searchModel' => $searchModel,
                    ])
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'guide-entrant-test-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'guide-entrant-test-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'guide-entrant-test-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'guide-entrant-test-grid',
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (GuideEntrantTest $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'division_id',
                        'filter' => RefBook::find('division_name')->getList(),
                        'value' => function (GuideEntrantTest $model) {

                            return RefBook::find('division_name')->getValue($model->division_id);
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    'name',
                    'name_dev',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [GuideEntrantTest::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                            [GuideEntrantTest::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                        ],
                        'options' => ['style' => 'width:60px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/guidestudy/entrant-test',
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


