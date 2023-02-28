<?php

use yii\widgets\Pjax;
use artsoft\grid\SortableGridView;
use artsoft\grid\GridQuickLinks;
use common\models\education\LessonMark;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\education\search\LessonMarkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Lesson Marks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-mark-index">
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
                                'model' => LessonMark::className(),
                                'searchModel' => $searchModel,
                            ])
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'lesson-mark-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'lesson-mark-grid-pjax',
                    ])
                    ?>

                    <?=
                    SortableGridView::widget([
                        'id' => 'piece-category-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'sortableAction' => ['grid-sort'],
                        'bulkActionOptions' => [
                            'gridId' => 'lesson-mark-grid',
                        ],
                        'columns' => [
                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (LessonMark $model) {
                                    return sprintf('#%06d', $model->id);
                                },
                            ],
                            [
                                'attribute' => 'mark_category',
                                'filter' => LessonMark::getMarkCatogoryList(),
                                'value' => function (LessonMark $model) {
                                    return LessonMark::getMarkCatogoryValue($model->mark_category);
                                },
                                'options' => ['style' => 'width:350px'],
                                'format' => 'raw',
                            ],
                            'mark_label',
                            'mark_hint',
                            'mark_value',
                            [
                                'class' => 'artsoft\grid\columns\StatusColumn',
                                'attribute' => 'status',
                                'optionsArray' => [
                                    [LessonMark::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                                    [LessonMark::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                                ],
                                'options' => ['style' => 'width:60px']
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    return [$action, 'id' => $model->id];
                                },
                                'controller' => '/guidestudy/lesson-mark',
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


