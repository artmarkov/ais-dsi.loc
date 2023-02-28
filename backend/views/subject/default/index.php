<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\subject\Subject;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subject\search\SubjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subjects');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-index">
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
                                'model' => Subject::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'subject-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'subject-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'subject-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'subject-grid',
//                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art','Delete')] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (Subject $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'name',
                                'options' => ['style' => 'width:350px'],
                                'value' => function (Subject $model) {
                                    return $model->name;
                                },
                            ],
                            'slug',
                            [
                                'attribute' => 'department_list',
                                'filter' => \artsoft\helpers\RefBook::find('department_name_dev')->getList(),
                                'value' => function (Subject $model) {
                                    $v = [];
                                    foreach ($model->department_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = \artsoft\helpers\RefBook::find('department_name_dev')->getValue($id) ?? '';
                                    }
                                    return implode(', ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'category_list',
                                'filter' => \artsoft\helpers\RefBook::find('subject_category_name_dev')->getList(),
                                'value' => function (Subject $model) {
                                    $v = [];
                                    foreach ($model->category_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = \artsoft\helpers\RefBook::find('subject_category_name_dev')->getValue($id) ?? null;
                                    }
                                    return implode(', ', $v);
                                },
                                'options' => ['style' => 'width:250px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'vid_list',
                                'filter' => \artsoft\helpers\RefBook::find('subject_vid_name_dev')->getList(),
                                'value' => function (Subject $model) {
                                    $v = [];
                                    foreach ($model->vid_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = \artsoft\helpers\RefBook::find('subject_vid_name_dev')->getValue($id) ?? '';
                                    }
                                    return implode(', ', $v);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [Subject::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [Subject::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:150px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/subject/default',
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


