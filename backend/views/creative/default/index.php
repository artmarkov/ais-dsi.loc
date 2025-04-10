<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\creative\CreativeWorks;
use artsoft\helpers\RefBook;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use common\models\own\Department;

/* @var $this yii\web\View */
/* @var $searchModel common\models\creative\search\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/creative', 'Creative Works');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="creative-works-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= GridQuickLinks::widget([
                                'model' => CreativeWorks::className(),
                                'searchModel' => $searchModel,
                                'labels' => [
                                    'all' => Yii::t('art', 'All'),
                                    'inactive' => Yii::t('art/creative', 'Closed'),
                                    'active' => Yii::t('art/creative', 'Open'),
                                ]
                            ]) ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'creative-works-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'creative-works-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'creative-works-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => \artsoft\Art::isBackend() ? [
                            'gridId' => 'creative-works-grid',
                            'actions' => [
                                Url::to(['bulk-activate']) => Yii::t('art/creative', 'Оpen for viewing'),
                                Url::to(['bulk-deactivate']) => Yii::t('art/creative', 'Close for viewing'),
                                Url::to(['bulk-delete']) => Yii::t('yii', 'Delete'),
                            ]
                        ] : false,
                        'rowOptions' => function(CreativeWorks $model) {
                            if($model->getFilesCount() > 0) {
                                return ['class' => 'success'];
                            }
                            return [];
                        },
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px'], 'visible' => \artsoft\Art::isBackend()],
                            [
                                'options' => ['style' => 'width:30px'],
                                'attribute' => 'id',
                                'value' => function (CreativeWorks $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                                'format' => 'raw'
                            ],
                            [
                                'options' => ['style' => 'width:800px'],
                                'attribute' => 'name',
                                'value' => function (CreativeWorks $model) {
                                    return $model->name;
                                },
                            ],
                            [
                                'attribute' => 'category_id',
                                'value' => 'categoryName',
                                'label' => Yii::t('art/creative', 'Creative Category'),
                                'filter' => \common\models\creative\CreativeCategory::getCreativeCategoryList(),
                            ],
                            [
                                'attribute' => 'department_list',
                                'filter' => Department::getDepartmentList(),
                                'value' => function (CreativeWorks $model) {
                                    $v = [];
                                    foreach ($model->department_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = Department::findOne($id)->name;
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'teachers_list',
                                'filter' =>  RefBook::find('teachers_fullname')->getList(),
                                'value' => function (CreativeWorks $model) {
                                    $v = [];
                                    foreach ($model->teachers_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = RefBook::find('teachers_fio')->getValue($id);
                                    }
                                    return implode('<br/> ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                                'visible' => \artsoft\Art::isBackend(),
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => CreativeWorks::getStatusOptionsList(),
                                'options' => ['style' => 'width:180px'],
                            ],
                            [
                                'attribute' => 'created_at',
                                'filter' => false,
                                'value' => function (CreativeWorks $model) {
                                    return '<span style="font-size:85%; " class="label label-primary">'
                                        . Yii::$app->formatter->asDatetime($model->created_at) . '</span>';
                                },
                                'format' => 'raw',
                                'options' => ['style' => 'width:150px;'],
                                'contentOptions' => ['style'=>"text-align:center; vertical-align: middle;"],
                            ],
//                            [
//                                'class' => '\artsoft\grid\columns\DateFilterColumn',
//                                'attribute' => 'published_at',
//                                'value' => function (CreativeWorks $model) {
//                                    return '<span style="font-size:85%;" class="label label-'
//                                        . ((time() >= $model->published_at) ? 'primary' : 'default') . '">'
//                                        . $model->published_at . '</span>';
//                                },
//                                'format' => 'raw',
//                                'options' => ['style' => 'width:150px'],
//                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/creative/default',
                                'template' => '{view} {update} {delete}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'visible' => \artsoft\Art::isBackend()

                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/teachers/creative',
                                'template' => '{view}',
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'visible' => \artsoft\Art::isFrontend()

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


