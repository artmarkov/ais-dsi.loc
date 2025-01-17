<?php

use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\concourse\Concourse;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\concourse\search\ConcourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Конкурсы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="concourse-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Concourse::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'concourse-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'concourse-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'concourse-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                /*'bulkActionOptions' => [
                    'gridId' => 'concourse-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],*/
                'columns' => [
//                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (Concourse $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'name',
                        'value' => function (Concourse $model) {
                            return $model->name;
                        },
                        'options' => ['style' => 'width:450px'],
                    ],
                    'timestamp_in:date',
                    'timestamp_out:date',
                    [
                        'attribute' => 'description',
                        'value' => function (Concourse $model) {
                            return $model->description;
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'author_id',
                        'filter' => artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'value' => function (Concourse $model) {
                            return $model->author->userCommon ? $model->author->userCommon->fullName : $model->author_id;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [Concourse::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                            [Concourse::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                        ],
                        'options' => ['style' => 'width:150px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/concourse/default',
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


