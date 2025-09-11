<?php

use common\models\teachers\Teachers;
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
$author_id = CreativeWorks::getAuthorId();
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
                            <?php
//                            GridQuickLinks::widget([
//                                'model' => CreativeWorks::className(),
//                                'searchModel' => $searchModel,
//                                'labels' => [
//                                    'all' => Yii::t('art', 'All'),
//                                    'inactive' => Yii::t('art/creative', 'Closed'),
//                                    'active' => Yii::t('art/creative', 'Open'),
//                                ]
//                            ])
                            ?>
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
//                                Url::to(['bulk-activate']) => Yii::t('art/creative', 'Оpen for viewing'),
//                                Url::to(['bulk-deactivate']) => Yii::t('art/creative', 'Close for viewing'),
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
//                            [
//                                'class' => 'artsoft\grid\columns\StatusColumn',
//                                'attribute' => 'status',
//                                'optionsArray' => CreativeWorks::getStatusOptionsList(),
//                                'options' => ['style' => 'width:180px'],
//                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'doc_status',
                                'optionsArray' => [
                                    [CreativeWorks::DOC_STATUS_DRAFT, Yii::t('art', 'Draft'), 'default'],
                                    [CreativeWorks::DOC_STATUS_AGREED, Yii::t('art', 'Agreed'), 'success'],
                                    [CreativeWorks::DOC_STATUS_WAIT, Yii::t('art', 'Wait'), 'warning'],
                                    [CreativeWorks::DOC_STATUS_MODIF, Yii::t('art', 'Modif'), 'warning'],
                                ],
                                'options' => ['style' => 'width:150px']
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
                                'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                                'width' => '90px',
                                'visible' => \artsoft\Art::isFrontend(),
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'update' => function ($key, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                            ['/teachers/creative/update', 'id' => $model->id], [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($key, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                            ['/teachers/consult-creative/delete', 'id' => $model->id], [
                                                'title' => Yii::t('art', 'Delete'),
                                                'aria-label' => Yii::t('art', 'Delete'),
                                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                ],
                                'visibleButtons' => [
                                    'update' => function ($model) use ($author_id) {
                                        return $author_id == $model->author_id;
                                    },

                                    'delete' => function ($model) use ($author_id) {
                                        return $author_id == $model->author_id;
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
    </div>
</div>


