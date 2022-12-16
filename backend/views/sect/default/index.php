<?php

use common\models\own\Department;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\subjectsect\SubjectSect;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subjectsect\search\SubjectSectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Sects');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-sect-index">
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
                                'model' => SubjectSect::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'subject-sect-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'subject-sect-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'id' => 'subject-sect-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'bulkActionOptions' => [
                            'gridId' => 'subject-sect-grid',
                            'actions' => [Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (SubjectSect $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                             [
                                'attribute' => 'sect_name',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => '/sect/default',
                                'options' => ['style' => 'width:350px'],
                                'title' => function (SubjectSect $model) {
                                    return Html::a($model->sect_name, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'buttonsTemplate' => '{update} {view} {delete}',
                            ],
                            [
                                'attribute' => 'union_id',
                                'filter' => RefBook::find('union_name')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('union_name')->getValue($model->union_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'subject_cat_id',
                                'filter' => RefBook::find('subject_category_name')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_category_name')->getValue($model->subject_cat_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'subject_id',
                                'filter' => RefBook::find('subject_name')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_name')->getValue($model->subject_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'subject_type_id',
                                'filter' => RefBook::find('subject_type_name')->getList(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_type_name')->getValue($model->subject_type_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'subject_vid_id',
                                'filter' => \common\models\subject\SubjectVid::getVidListGroup(),
                                'value' => function (SubjectSect $model) {
                                    return RefBook::find('subject_vid_name')->getValue($model->subject_vid_id);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'sub_group_qty',
                            [
                                'class' => 'kartik\grid\ActionColumn',
//                                'urlCreator' => function ($action, $model, $key, $index) {
//                                    return [$action, 'id' => $key];
//                                },
                                'controller' => '/sect/default',
                                // 'template' => '{view} {update} {delete}',
//                                'headerOptions' => ['class' => 'kartik-sheet-style'],
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


